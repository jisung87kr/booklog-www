<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CategoryTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')
            ->orderBy('parent_id')
            ->orderBy('name')
            ->get();

        return view('admin.categories', compact('categories'));
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => ['required', Rule::enum(CategoryTypeEnum::class)],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        Category::create([
            'type' => $request->type,
            'name' => $request->name,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.categories')->with('success', '카테고리가 생성되었습니다.');
    }

    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'type' => ['required', Rule::enum(CategoryTypeEnum::class)],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        // 자신을 부모로 설정하는 것을 방지
        if ($request->parent_id == $category->id) {
            return redirect()->back()->with('error', '자신을 부모 카테고리로 설정할 수 없습니다.');
        }

        $category->update([
            'type' => $request->type,
            'name' => $request->name,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.categories')->with('success', '카테고리가 업데이트되었습니다.');
    }

    public function destroy(Category $category)
    {
        // 하위 카테고리가 있는지 확인
        $childrenCount = $category->child()->count();

        if ($childrenCount > 0) {
            return redirect()->back()->with('error', "이 카테고리에 {$childrenCount}개의 하위 카테고리가 있어 삭제할 수 없습니다. 먼저 하위 카테고리를 삭제하거나 이동시켜주세요.");
        }

        // 카테고리를 사용하는 게시물이 있는지 확인
        $postsCount = $category->posts()->count();
        if ($postsCount > 0) {
            return redirect()->back()->with('error', "이 카테고리를 사용하는 {$postsCount}개의 게시물이 있어 삭제할 수 없습니다.");
        }

        $categoryName = $category->name;
        $category->delete();

        return redirect()->route('admin.categories')->with('success', "{$categoryName} 카테고리가 삭제되었습니다.");
    }

    public function toggle(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        $status = $category->is_active ? '활성화' : '비활성화';
        return redirect()->back()->with('success', "카테고리가 {$status}되었습니다.");
    }
}