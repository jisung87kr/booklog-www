<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\File;

class AttachmentService{
    protected $storagePath = 'public/attachment';

    public function storeFile($model, File $file, $params=[], $storagePath=null)
    {
        $storagePath = $storagePath ?? $this->storagePath;
        $path = $file->store($storagePath);
        return $model->attachments()->create([
            'file_name' => $file->hashName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);
    }
}
