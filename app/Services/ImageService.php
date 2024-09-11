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

    public function storeImages($model, $images, $params=[], $storagePath=null){
        if($images && is_array($images)){
            request()->validate([
                'images.*' => ['required', 'mimes:jpg,jpeg,png,gif', 'max:20480'],
            ]);

            foreach ($images as $image) {
                if($image->isValid()){
                    $uploadedImage = $this->storeImage($model, $image, $params, $storagePath);
                    $uploadedImages[] = $uploadedImage;
                }
            }

            return $uploadedImages;
        }
        return [];
    }
}
