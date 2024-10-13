<?php
namespace App\Services;

use App\Models\Book;
use App\Models\Post;
use App\Models\ReadingProcess;

class MorphService{
    public function getMorphModel(string $type, $id)
    {
        switch ($type){
            case 'books':
                return Book::findOrFail($id);
            case 'processes':
                return ReadingProcess::findOrFail($id);
            case 'posts':
                return Post::findOrFail($id);
            default:
                abort(404);
        }
    }
}
