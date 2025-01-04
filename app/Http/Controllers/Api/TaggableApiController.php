<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Tag;
use App\Services\MorphService;
use Illuminate\Http\Request;

class TaggableApiController extends Controller
{

    private MorphService $morphService;

    public function __construct(MorphService $morphService)
    {
        $this->morphService = $morphService;
    }

    public function sync(Request $request, string $type, $id)
    {
        try {
            $validated = $request->validate([
                'tag' => ['required', 'array'],
                'tag.*.name' => ['required'],
            ]);

            Tag::upsert($validated['tag'], ['name'], ['name']);
            $tagIds = Tag::whereIn('name', collect($validated['tag'])->pluck('name'))->pluck('id');
            $model = $this->morphService->getMorphModel($type, $id);
            $model->tags()->sync($tagIds);
            return response()->success('', $tagIds);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }
}
