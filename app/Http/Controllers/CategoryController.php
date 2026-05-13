<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.dashboard');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:categories,name',
        ]);

        Category::create(['name' => $request->name]);

        return back()->with('success', 'Category added successfully!');
    }

    public function destroy(Category $category)
    {
        if ($category->books()->exists()) {
            return back()->with('error', 'Cannot delete category: it has books assigned to it.');
        }

        $category->delete();

        return back()->with('success', 'Category removed!');
    }

    public function create() {}

    public function show(Category $category) {}

    public function edit(Category $category) {}

    public function update(Request $request, Category $category) {}
}
