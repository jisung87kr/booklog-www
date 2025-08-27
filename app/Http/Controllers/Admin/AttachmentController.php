<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AttachmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\Attachment;

class AttachmentController extends Controller
{
    private $attachmentService;
    public function __construct(AttachmentService $attachmentService){
        $this->attachmentService = $attachmentService;
    }

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:51200|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,txt,zip,rar,7z,hwp,xls,xlsx'
        ], [
            'file.required' => 'file 항목은 필수 항목입니다.',
            'file.file' => '유효한 파일을 업로드해주세요.',
            'file.max' => '파일 크기는 50MB를 초과할 수 없습니다.',
            'file.mimes' => '지원하지 않는 파일 형식입니다. (JPG, PNG, GIF, WEBP, PDF, DOC, DOCX, HWP, TXT, ZIP, RAR, 7Z만 허용)'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $size = $file->getSize();
            $mimeType = $file->getMimeType();

            // 파일 이름 생성 (중복 방지를 위해 UUID 사용)
            $filename = Str::uuid() . '.' . $extension;

            // 첨부파일 저장 경로
            $path = $file->storeAs('attachments', $filename, 'public');

            // 데이터베이스에 임시 첨부파일 정보 저장 (다형성 관계로 임시 저장)
            $attachment = Attachment::create([
                'attachmentable_type' => 'temp',
                'attachmentable_id' => 0,
                'file_name' => $filename,
                'original_name' => $originalName,
                'file_path' => $path,
                'file_size' => $size,
                'mime_type' => $mimeType,
                'sort_order' => 0,
            ]);

            return response()->json([
                'success' => true,
                'file' => [
                    'id' => $attachment->id,
                    'file_name' => $originalName,
                    'file_size' => $size,
                    'url' => Storage::url($path),
                    'mime_type' => $mimeType,
                    'sort_order' => 0,
                    'is_image' => $this->isImageFile($mimeType)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '파일 업로드 중 오류가 발생했습니다: ' . $e->getMessage()
            ], 500);
        }
    }

    public function delete(Attachment $attachment)
    {
        try {
            // 파일 삭제
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            // 데이터베이스에서 삭제
            $attachment->delete();

            return response()->json([
                'success' => true,
                'message' => '첨부파일이 삭제되었습니다.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '첨부파일 삭제 중 오류가 발생했습니다: ' . $e->getMessage()
            ], 500);
        }
    }

    private function isImageFile($mimeType)
    {
        return in_array($mimeType, [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp'
        ]);
    }

    public function reorderAttachments(Request $request)
    {
        $request->validate([
            'attachment_ids' => 'required|array',
            'attachment_ids.*' => 'exists:attachments,id',
        ]);

        try {
            foreach ($request->attachment_ids as $index => $attachmentId) {
                Attachment::where('id', $attachmentId)
                    ->update(['sort_order' => $index + 1]);
            }
            
            return response()->json([
                'success' => true,
                'message' => '파일 순서가 업데이트되었습니다.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '파일 순서 업데이트에 실패했습니다: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPostAttachments($postId)
    {
        try {
            $attachments = Attachment::where('attachmentable_type', 'App\\Models\\Post')
                ->where('attachmentable_id', $postId)
                ->orderBy('sort_order', 'asc')
                ->get()
                ->map(function ($attachment) {
                    return [
                        'id' => $attachment->id,
                        'file_name' => $attachment->original_name ?: $attachment->file_name,
                        'file_size' => $attachment->file_size,
                        'mime_type' => $attachment->mime_type,
                        'sort_order' => $attachment->sort_order,
                        'url' => Storage::url($attachment->file_path),
                        'is_image' => $this->isImageFile($attachment->mime_type)
                    ];
                });

            return response()->json([
                'success' => true,
                'files' => $attachments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '첨부파일 조회 중 오류가 발생했습니다: ' . $e->getMessage()
            ], 500);
        }
    }


}
