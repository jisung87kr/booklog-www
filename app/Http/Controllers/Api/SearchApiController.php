<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\SearchService;
use Illuminate\Http\Request;

class SearchApiController extends Controller{
    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }
    public function index(Request $request)
    {
        try {
            $result = $this->searchService->search($request->input('q'));
            return response()->success('', $result);
        } catch (\Exception $e) {
            return response()->error('', []);
        }
    }
}
