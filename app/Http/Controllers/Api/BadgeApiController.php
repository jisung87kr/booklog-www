<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Badge;
use Illuminate\Http\Request;

class BadgeApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $badges = Badge::paginate(30);
            return response()->success('', $badges);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'description' => 'required',
            ]);

            $badge = Badge::create($validated);

            return response()->success('', $badge);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Badge $badge)
    {
        try {
            return response()->success('', $badge);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Badge $badge)
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable',
                'description' => 'nullable',
            ]);
            $badge->update($validated);
            return response()->success('', $badge);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Badge $badge)
    {
        try {
            $badge->delete();
            return response()->success('');
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }
}
