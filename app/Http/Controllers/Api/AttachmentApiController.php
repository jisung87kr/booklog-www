<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Post;
use App\Models\ReadingProcess;
use App\Services\AttachmentService;
use App\Services\MorphService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AttachmentApiController extends Controller
{
    private MorphService $morphService;
    private AttachmentService $attachmentService;

    public function __construct(MorphService $morphService, AttachmentService $attachmentService)
    {
        $this->morphService = $morphService;
        $this->attachmentService = $attachmentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(string $type, $id)
    {
        try {
            $model = $this->morphService->getMorphModel($type, $id);
            return ApiResponse::success('', $model->attachments);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $type, $id)
    {
        try {
            $model = $this->morphService->getMorphModel($type, $id);

            $files = $request->file('files');
            $uploadedFiles = [];

            if($files && is_array($files)){
                $request->validate([
                    'files.*' => 'required|mimes:jpg,jpeg,png,gif,zip,pdf|max:5096',
                ], [
                    'files.*.required' => '파일은 필수입니다.',
                    'files.*.mimes' => '허용되지 않는 파일 형식입니다. jpg, jpeg, png, gif, zip, pdf만 가능합니다.',
                    'files.*.max' => '파일 크기가 5MB를 초과할 수 없습니다.',
                ]);

                foreach ($files as $file) {
                    if($file->isValid()){
                        $uploadedFiles[] = $this->attachmentService->storeFile($model, $file, $request->all());
                    }
                }
            }
            return ApiResponse::success('wqe', $uploadedFiles);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return ApiResponse::error('', $e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $type, $id)
    {
        try {
            $model = $this->morphService->getMorphModel($type, $id);
            return ApiResponse::success();
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }
}
