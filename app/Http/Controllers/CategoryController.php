<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('balances/show', [
            // UPDATE: Filter categories by user
            'categories' => Category::where('user_id', Auth::id())
                ->orderBy('type')
                ->orderBy('name')
                ->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:7',
        ]);

        // UPDATE: Check if category name already exists for this user and type
        $existingCategory = Category::where('user_id', Auth::id())
            ->where('name', $validated['name'])
            ->where('type', $validated['type'])
            ->first();

        if ($existingCategory) {
            return redirect()->back()
                ->withErrors(['name' => 'A category with this name already exists for this type.']);
        }

        // UPDATE: Create category with user_id
        Category::create([
            ...$validated,
            'user_id' => Auth::id()
        ]);

        return redirect()->back()
            ->with('success', 'Category added successfully');
    }

    // Method untuk update category (jika diperlukan dari halaman balances/show)
    public function update(Request $request, Category $category)
    {
        // Check if category belongs to current user
        if ($category->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:7',
        ]);

        // Check if category name already exists for this user and type (excluding current category)
        $existingCategory = Category::where('user_id', Auth::id())
            ->where('name', $validated['name'])
            ->where('type', $validated['type'])
            ->where('id', '!=', $category->id)
            ->first();

        if ($existingCategory) {
            return redirect()->back()
                ->withErrors(['name' => 'A category with this name already exists for this type.']);
        }

        $category->update($validated);

        return redirect()->back()
            ->with('success', 'Category updated successfully');
    }

    // Method untuk delete category (jika diperlukan dari halaman balances/show)
    public function destroy(Category $category)
    {
        // Check if category belongs to current user
        if ($category->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if category has transactions
        if ($category->transactions()->count() > 0) {
            return redirect()->back()
                ->withErrors(['error' => 'Cannot delete category that has transactions.']);
        }

        $category->delete();

        return redirect()->back()
            ->with('success', 'Category deleted successfully');
    }

    // Method untuk mendapatkan categories berdasarkan type (untuk AJAX/API calls)
    public function getByType(Request $request)
    {
        $type = $request->get('type');
        
        $categories = Category::where('user_id', Auth::id())
            ->when($type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }
}