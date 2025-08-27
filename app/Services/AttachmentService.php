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
        
        $sortOrder = $params['sort_order'] ?? $this->getNextSortOrder($model);
        
        return $model->attachments()->create([
            'file_name' => $file->hashName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'sort_order' => $sortOrder,
        ]);
    }
    
    public function getNextSortOrder($model)
    {
        return $model->attachments()->max('sort_order') + 1;
    }
    
    public function updateSortOrder($model, $attachmentIds)
    {
        foreach ($attachmentIds as $index => $attachmentId) {
            $model->attachments()
                ->where('id', $attachmentId)
                ->update(['sort_order' => $index + 1]);
        }
    }
}
