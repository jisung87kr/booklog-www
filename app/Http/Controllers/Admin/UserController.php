<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Persona;
use App\Models\User;
use App\Services\PersonaFeedService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected PersonaFeedService $feedService;

    public function __construct(PersonaFeedService $feedService)
    {
        $this->feedService = $feedService;
    }

    public function index()
    {
        $users = User::withoutGlobalScopes()->with('persona')->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function create()
    {
        $personas = Persona::where('is_active', true)->get();
        return view('admin.users.create', compact('personas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'nullable|string|max:255|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'persona_id' => 'nullable|exists:personas,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'persona_id' => $request->persona_id,
            'email_verified_at' => now(), // 관리자가 생성하는 사용자는 즉시 인증
        ]);

        return redirect()->route('admin.users')->with('success', '새 사용자가 생성되었습니다.');
    }

    public function edit(User $user)
    {
        // 글로벌 스코프 없이 사용자 로드
        $user = User::withoutGlobalScopes()->find($user->id);
        $personas = Persona::where('is_active', true)->get();
        return view('admin.users.edit', compact('user', 'personas'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'persona_id' => 'nullable|exists:personas,id',
        ]);

        // 비밀번호 필드가 있는 경우에만 유효성 검사 추가
        $validationRules = [];
        if ($request->filled('password')) {
            $validationRules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($validationRules);

        // 기본 정보 업데이트
        $updateData = $request->only(['name', 'email', 'username', 'persona_id']);

        // 비밀번호가 제공된 경우에만 업데이트
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        $user->update($updateData);

        $message = '사용자 정보가 업데이트되었습니다.';
        if ($request->filled('password')) {
            $message = '사용자 정보 및 비밀번호가 업데이트되었습니다.';
        }

        return redirect()->route('admin.users')->with('success', $message);
    }

    public function destroy(User $user)
    {
        // 관리자 자신을 삭제하지 못하도록 방지
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', '자신의 계정은 삭제할 수 없습니다.');
        }

        // 사용자와 관련된 데이터 확인
        $postsCount = $user->posts()->count();
        $commentsCount = $user->comments()->count();

        if ($postsCount > 0 || $commentsCount > 0) {
            return redirect()->back()->with('error', "이 사용자는 {$postsCount}개의 포스트와 {$commentsCount}개의 댓글을 작성했습니다. 데이터를 먼저 정리한 후 삭제해주세요.");
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users')->with('success', "{$userName} 사용자가 삭제되었습니다.");
    }

    public function assignPersona(Request $request, $userId)
    {
        $request->validate([
            'persona_id' => 'required|exists:personas,id',
        ]);

        $user = User::withoutGlobalScopes()->findOrFail($userId);
        $persona = Persona::find($request->persona_id);
        $user->update(['persona_id' => $request->persona_id]);

        // Ajax 요청인 경우 JSON 응답
        if ($request->expectsJson()) {
            return ApiResponse::success('페르소나가 할당되었습니다.', [
                'user_id' => $user->id,
                'persona_id' => $persona->id,
                'persona_name' => $persona->name,
            ]);
        }

        return redirect()->back()->with('success', "{$user->name}에게 {$persona->name} 페르소나가 할당되었습니다.");
    }

    public function removePersona($userId)
    {
        $user = User::withoutGlobalScopes()->findOrFail($userId);
        $user->update(['persona_id' => null]);
        return redirect()->back()->with('success', "{$user->name}의 페르소나 할당이 해제되었습니다.");
    }

    public function bulkAssignPersona(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'persona_id' => 'required|exists:personas,id',
        ]);

        $persona = Persona::find($request->persona_id);
        $updatedCount = User::withoutGlobalScopes()->whereIn('id', $request->user_ids)
            ->update(['persona_id' => $request->persona_id]);

        return redirect()->back()->with('success', "{$updatedCount}명의 사용자에게 {$persona->name} 페르소나가 할당되었습니다.");
    }

    public function generateFeed(User $user)
    {
        if (!$user->persona_id) {
            return redirect()->back()->with('error', '페르소나가 할당되지 않은 사용자입니다.');
        }

        try {
            $this->feedService->generateFeedForPersona($user);
            return redirect()->back()->with('success', "{$user->name}의 AI 피드가 생성되었습니다.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '피드 생성 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }
}