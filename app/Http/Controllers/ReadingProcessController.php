<?php

namespace App\Http\Controllers;

use App\Models\ReadingProcess;
use Illuminate\Http\Request;

class ReadingProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('readingProcess.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ReadingProcess $readingProcess)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReadingProcess $readingProcess)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReadingProcess $readingProcess)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReadingProcess $readingProcess)
    {
        //
    }
}
