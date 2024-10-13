<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserActionEnum;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Models\UserAction;
use App\Services\MorphService;
use Illuminate\Http\Request;
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
            return ApiResponse::success('', $actions);
        } catch (\Exception $e){
            return ApiResponse::error('', $e->getMessage());
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'action' => ['required', Rule::enum(UserActionEnum::class)],
                'user_actionable_id' => 'required',
                'user_actionable_type' => 'required',
            ]);

            $model = $this->morphService->getMorphModel($validated['user_actionable_type'], $validated['user_actionable_id']);
            $validated['user_actionable_type'] = $model::class;
            $action = $request->user()->actions()->create($validated);
            return ApiResponse::success('', $action);
        } catch (\Exception $e){
            return ApiResponse::error('', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user, UserAction $action)
    {
        try {
            return ApiResponse::success('', $action);
        } catch (\Exception $e){
            return ApiResponse::error('', $e->getMessage());
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
            return ApiResponse::success('', $action);
        } catch (\Exception $e){
            return ApiResponse::error('', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserAction $action)
    {
        try {
            $action->delete();
            return ApiResponse::success('', '');
        } catch (\Exception $e){
            return ApiResponse::error('', $e->getMessage());
        }
    }
}
