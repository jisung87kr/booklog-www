<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserBookcase;
use Illuminate\Http\Request;

class UserBookcaseController extends Controller
{
    public function create(User $user)
    {
        $bookcase = new UserBookcase();
        return view('bookcase/create', compact('user', 'bookcase'));
    }
    public function show(User $user , UserBookcase $bookcase)
    {
        $bookcase->load(['books', 'user']);
        return view('bookcase/show', compact('user', 'bookcase'));
    }

    public function edit(User $user , UserBookcase $bookcase)
    {
        $bookcase->load(['books', 'user']);
        return view('bookcase/edit', compact('user', 'bookcase'));
    }
}
