<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserActionEnum;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Models\UserAction;
use App\Services\MorphService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserActionApiController extends Controller
{
    private $morphService;

    public function __construct(MorphService $morphService)
    {
        $this->morphService = $morphService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(User $user)
    {
        try {
            $actions = request()->user()->actions()->orderBy('id', 'desc')->paginate(30);
            return response()->success('', $actions);
        } catch (\Exception $e){
            return response()->error('', $e->getMessage());
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'action' => ['required', Rule::enum(UserActionEnum::class)],
                'user_actionable_id' => 'required',
                'user_actionable_type' => 'required',
            ]);

            $model = $this->morphService->getMorphModel($validated['user_actionable_type'], $validated['user_actionable_id']);
            $validated['user_actionable_type'] = $model::class;
            if($request->user()){
                $action = $request->user()->actions()->create($validated);
            } else {
                if($validated['action'] == UserActionEnum::SHARE->value){
                    $action = UserAction::create($validated);
                } else {
                    throw new \Exception('로그인이 필요한 서비스입니다.');
                }
            }

            return response()->success('', $action);
        } catch (\Exception $e){
            Log::error($e->getMessage(), $e->getTrace());
            return response()->error('', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user, UserAction $action)
    {
        try {
            return response()->success('', $action);
        } catch (\Exception $e){
            return response()->error('', $e->getMessage());
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user, UserAction $action)
    {
        try {
            $validated = $request->validate([
                'action' => ['required', Rule::enum(UserActionEnum::class)],
                'user_actionable_id' => 'required',
                'user_actionable_type' => 'required',
            ]);

            $model = $this->morphService->getMorphModel($validated['user_actionable_type'], $validated['user_actionable_id']);
            $validated['user_actionable_type'] = $model::class;
            $action->update($validated);
            return response()->success('', $action);
        } catch (\Exception $e){
            return response()->error('', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, UserAction $action)
    {
        try {
            $action->delete();
            return response()->success('', '');
        } catch (\Exception $e){
            return response()->error('', $e->getMessage());
        }
    }
}
