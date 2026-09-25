<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Category::class);

        $categories = Category::withCount('inventories')
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Settings/Categories/Index', [
            'categories' => $categories,
            'filters' => $request->only('search'),
        ]);
    }

    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->validated());

        return back()->with('success', 'Category created.');
    }

    public function update(StoreCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return back()->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);

        if ($category->inventories()->exists()) {
            return back()->with('error', 'Cannot delete a category that still has assets.');
        }

        $category->delete();

        return back()->with('success', 'Category removed.');
    }
}
