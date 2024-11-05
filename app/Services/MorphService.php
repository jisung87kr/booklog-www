<?php
namespace App\Services;

use App\Models\Book;
use App\Models\Post;
use App\Models\ReadingProcess;
use App\Models\User;

class MorphService{
    public function getMorphModel(string $type, $id)
    {
        switch ($type){
            case 'book':
                return Book::findOrFail($id);
            case 'readingProcess':
                return ReadingProcess::findOrFail($id);
            case 'post':
                return Post::findOrFail($id);
            case 'user':
                return User::findOrFail($id);
            default:
                abort(404);
        }
    }
}
