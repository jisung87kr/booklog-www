<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserBookcase;
use Illuminate\Http\Request;

class UserBookcaseController extends Controller
{
    public function show(User $user , UserBookcase $bookcase)
    {
        $bookcase->load(['books', 'user']);
        return view('bookcase', compact('user', 'bookcase'));
    }
}
