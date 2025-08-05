<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Services\PersonaFeedService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PersonaController extends Controller
{
    protected PersonaFeedService $feedService;

    public function __construct(PersonaFeedService $feedService)
    {
        $this->feedService = $feedService;
    }

    public function index(): JsonResponse
    {
        $personas = Persona::where('is_active', true)->get();
        
        return response()->json([
            'success' => true,
            'data' => $personas
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'age' => 'required|integer|min:1|max:120',
            'occupation' => 'required|string|max:255',
            'reading_preferences' => 'required|array',
            'description' => 'nullable|string',
            'speaking_style' => 'nullable|string|max:255',
        ]);

        $persona = Persona::create($validated);

        return response()->json([
            'success' => true,
            'data' => $persona,
            'message' => '페르소나가 성공적으로 생성되었습니다.'
        ], 201);
    }

    public function show(Persona $persona): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $persona
        ]);
    }

    public function update(Request $request, Persona $persona): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'gender' => 'sometimes|in:male,female,other',
            'age' => 'sometimes|integer|min:1|max:120',
            'occupation' => 'sometimes|string|max:255',
            'reading_preferences' => 'sometimes|array',
            'description' => 'nullable|string',
            'speaking_style' => 'sometimes|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        $persona->update($validated);

        return response()->json([
            'success' => true,
            'data' => $persona,
            'message' => '페르소나가 성공적으로 업데이트되었습니다.'
        ]);
    }

    public function destroy(Persona $persona): JsonResponse
    {
        $persona->delete();

        return response()->json([
            'success' => true,
            'message' => '페르소나가 성공적으로 삭제되었습니다.'
        ]);
    }

    public function generateFeed(Persona $persona): JsonResponse
    {
        try {
            $feed = $this->feedService->generateFeedForPersona($persona);
            
            return response()->json([
                'success' => true,
                'data' => $feed,
                'message' => '페르소나 피드가 성공적으로 생성되었습니다.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '피드 생성 중 오류가 발생했습니다: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateAllFeeds(): JsonResponse
    {
        try {
            $feeds = $this->feedService->generateFeedsForAllActivePersonas();
            
            return response()->json([
                'success' => true,
                'data' => $feeds,
                'message' => "{$feeds->count()}개의 페르소나 피드가 생성되었습니다."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '피드 생성 중 오류가 발생했습니다: ' . $e->getMessage()
            ], 500);
        }
    }
}