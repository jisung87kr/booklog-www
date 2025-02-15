<?php

namespace App\Models\Api;

use App\Http\Responses\ApiResponse;
use App\Models\Image;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageService;

class ImageApiController extends Model
{
    use HasFactory;

    private $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index(string $type, $id)
    {
        try {
            $model = $this->getCommentableModel($type, $id);
            $images = $model->images()->paginate(10);
            return response()->success('', $images);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    public function store(Request $request, string $type, $id)
    {
        try {
            $model = $this->getCommentableModel($type, $id);
            $images = $request->file('images');
            $uploadedImages = $this->imageService->storeImages($model, $images, $request->all());
            return response()->success('', $uploadedImages);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    public function removeImage(string $type, $id, Image $image){
        try {
            Storage::delete($image->file_path);
            $image->delete();
            return response()->success('', '');
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    public function getCommentableModel(string $type, $id)
    {
        switch ($type){
            case 'post':
                return Post::findOrFail($id);
            default:
                abort(404);
        }
    }
}
