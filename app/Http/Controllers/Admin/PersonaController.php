<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\User;
use App\Services\PersonaFeedService;
use Illuminate\Http\Request;

class PersonaController extends Controller
{
    protected PersonaFeedService $feedService;

    public function __construct(PersonaFeedService $feedService)
    {
        $this->feedService = $feedService;
    }

    public function index()
    {
        $personas = Persona::all();

        // 각 페르소나별 사용자 수를 별도로 계산
        foreach ($personas as $persona) {
            $persona->users_count = User::withoutGlobalScopes()->where('persona_id', $persona->id)->count();
        }

        return view('admin.personas', compact('personas'));
    }

    public function create()
    {
        return view('admin.personas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'age' => 'required|integer|min:1|max:120',
            'occupation' => 'required|string|max:255',
            'description' => 'nullable|string',
            'speaking_style' => 'nullable|string|max:255',
            'reading_preferences' => 'nullable|json',
        ]);

        $readingPreferences = [
            'genres' => $request->input('genres'),
            'authors' => explode(',', $request->input('authors', '')),
            'keywords' => explode(',', $request->input('keywords', '')),
            'reading_speed' => $request->input('reading_speed', 'average'),
            'preferred_length' => $request->input('preferred_length', 'medium'),
        ];

        Persona::create([
            'name' => $request->name,
            'gender' => $request->gender,
            'age' => $request->age,
            'occupation' => $request->occupation,
            'description' => $request->description,
            'speaking_style' => $request->speaking_style,
            'reading_preferences' => $readingPreferences,
            'is_active' => true,
        ]);

        return redirect()->route('admin.personas')->with('success', '페르소나가 생성되었습니다.');
    }

    public function edit(Persona $persona)
    {
        return view('admin.personas.edit', compact('persona'));
    }

    public function update(Request $request, Persona $persona)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'age' => 'required|integer|min:1|max:120',
            'occupation' => 'required|string|max:255',
            'description' => 'nullable|string',
            'speaking_style' => 'nullable|string|max:255',
            'reading_preferences' => 'nullable|json',
        ]);

        $readingPreferences = [
            'genres' => $request->input('genres'),
            'authors' => explode(',', $request->input('authors', '')),
            'keywords' => explode(',', $request->input('keywords', '')),
            'reading_speed' => $request->input('reading_speed', 'average'),
            'preferred_length' => $request->input('preferred_length', 'medium'),
        ];

        $persona->update([
            'name' => $request->name,
            'gender' => $request->gender,
            'age' => $request->age,
            'occupation' => $request->occupation,
            'description' => $request->description,
            'speaking_style' => $request->speaking_style,
            'reading_preferences' => $readingPreferences,
        ]);

        return redirect()->route('admin.personas')->with('success', '페르소나가 업데이트되었습니다.');
    }

    public function destroy(Persona $persona)
    {
        // 페르소나를 사용 중인 사용자가 있는지 확인
        $usersCount = $persona->users()->count();

        if ($usersCount > 0) {
            return redirect()->back()->with('error', "이 페르소나를 사용 중인 {$usersCount}명의 사용자가 있어 삭제할 수 없습니다.");
        }

        $persona->delete();
        return redirect()->route('admin.personas')->with('success', '페르소나가 삭제되었습니다.');
    }

    public function toggle(Persona $persona)
    {
        $persona->update(['is_active' => !$persona->is_active]);

        $status = $persona->is_active ? '활성화' : '비활성화';
        return redirect()->back()->with('success', "페르소나가 {$status}되었습니다.");
    }

    public function updateSchedule(Request $request)
    {
        // 디버깅을 위한 로깅
        logger()->info('페르소나 스케줄 업데이트 요청', [
            'request_data' => $request->all(),
            'persona_id' => $request->persona_id
        ]);

        $request->validate([
            'persona_id' => 'required|exists:personas,id',
            'auto_publish_enabled' => 'boolean',
            'publish_frequency' => 'nullable|in:hourly,daily,weekly',
            'daily_hour' => 'nullable|integer|min:0|max:23',
            'daily_minute' => 'nullable|integer|min:0|max:59',
            'weekly_day' => 'nullable|integer|min:0|max:6',
            'weekly_hour' => 'nullable|integer|min:0|max:23',
            'weekly_minute' => 'nullable|integer|min:0|max:59',
        ]);

        $persona = Persona::findOrFail($request->persona_id);

        $autoPublishEnabled = $request->boolean('auto_publish_enabled');
        $publishFrequency = $autoPublishEnabled ? $request->publish_frequency : null;
        $publishSchedule = [];

        if ($autoPublishEnabled && $publishFrequency) {
            switch ($publishFrequency) {
                case 'daily':
                    $publishSchedule = [
                        'hour' => (int) $request->daily_hour,
                        'minute' => (int) $request->daily_minute,
                    ];
                    break;

                case 'weekly':
                    $publishSchedule = [
                        'day_of_week' => (int) $request->weekly_day,
                        'hour' => (int) $request->weekly_hour,
                        'minute' => (int) $request->weekly_minute,
                    ];
                    break;

                case 'hourly':
                    $publishSchedule = [];
                    break;
            }
        }

        $persona->update([
            'auto_publish_enabled' => $autoPublishEnabled,
            'publish_frequency' => $publishFrequency,
            'publish_schedule' => $publishSchedule,
        ]);

        // 다음 발행 시간 계산 및 업데이트
        $persona->updateNextPublishTime();

        // 저장 후 확인 로깅
        $persona->refresh();
        logger()->info('페르소나 스케줄 업데이트 완료', [
            'persona_id' => $persona->id,
            'auto_publish_enabled' => $persona->auto_publish_enabled,
            'publish_frequency' => $persona->publish_frequency,
            'publish_schedule' => $persona->publish_schedule,
            'next_publish_at' => $persona->next_publish_at
        ]);

        $message = $autoPublishEnabled
            ? "{$persona->name}의 자동 발행이 설정되었습니다. ({$persona->schedule_description})"
            : "{$persona->name}의 자동 발행이 비활성화되었습니다.";

        return redirect()->back()->with('success', $message);
    }

    public function generateFeeds()
    {
        try {
            // 활성화된 페르소나들에 대해 피드 생성
            $personas = Persona::where('is_active', true)->get();
            $generatedCount = 0;
            $errors = [];

            foreach ($personas as $persona) {
                try {
                    // 페르소나에 할당된 사용자가 있는지 확인
                    $user = $persona->users()->inRandomOrder()->first();
                    if ($user) {
                        $this->feedService->generateFeedForPersona($user);
                        $generatedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "{$persona->name}: " . $e->getMessage();
                    logger()->error("페르소나 피드 생성 실패", [
                        'persona_id' => $persona->id,
                        'persona_name' => $persona->name,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $message = "{$generatedCount}개의 AI 피드가 생성되었습니다.";
            if (!empty($errors)) {
                $errorCount = count($errors);
                $message .= " (오류 {$errorCount}건: " . implode(', ', array_slice($errors, 0, 3));
                if ($errorCount > 3) {
                    $message .= " 외 " . ($errorCount - 3) . "건";
                }
                $message .= ")";
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            logger()->error("AI 피드 일괄 생성 실패", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'AI 피드 생성 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }
}
