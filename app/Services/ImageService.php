<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImageService{
    protected $storagePath = 'public/image';

    public function storeImage($model, $image, $params=[], $storagePath=null)
    {
        $storagePath = $storagePath ?? $this->storagePath;
        $path = $image->store($storagePath);
        list($width, $height) = getimagesize(Storage::path($path));
        return $model->images()->create([
            'file_name' => $image->hashName(),
            'file_path' => $path,
            'file_size' => $image->getSize(),
            'width' => $width,
            'height' => $height,
            'alt_text' => $params['alt_text'] ?? null,
            'title' => $params['title'] ?? null,
            'description' => $params['description'] ?? null,
        ]);
    }
}
