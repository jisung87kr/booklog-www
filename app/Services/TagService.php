<?php
namespace App\Services;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagService{
    public function handleTags(Request $request, $model)
    {
        $tagValidated = $request->validate([
            'tag' => ['array', 'nullable'],
            'tag.*.name' => 'required',
        ]);

        if(!isset($tagValidated['tag'])){
            return false;
        }

        foreach ($tagValidated['tag'] as $item) {
            Tag::updateOrCreate(['name' => $item['name']], ['name' => $item['name']]);
        }

        $tags = Tag::whereIn('name', collect($tagValidated['tag'])->pluck('name')->toArray())->get()->pluck('id')->toArray();
        $model->tags()->sync($tags);
        return $tags;
    }
}
