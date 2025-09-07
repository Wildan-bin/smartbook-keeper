<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CategoryApiController extends Controller
{
    /**
     * List semua kategori user (bisa filter type: income/expense)
     */
    public function index(Request $request)
    {
        try {
            $query = Category::where('user_id', Auth::id());

            if ($request->has('type') && in_array($request->get('type'), ['income', 'expense'])) {
                $query->where('type', $request->get('type'));
            }

            $categories = $query->orderBy('name')->get();

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buat kategori baru
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:income,expense',
                'icon' => 'nullable|string|max:10',
                'color' => 'nullable|string|max:7',
            ]);

            $category = Category::create([
                'user_id' => Auth::id(),
                'name' => $validated['name'],
                'type' => $validated['type'],
                'icon' => $validated['icon'] ?? null,
                'color' => $validated['color'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detail kategori
     */
    public function show($id)
    {
        try {
            $category = Category::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $category
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update kategori
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Category::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:income,expense',
                'icon' => 'nullable|string|max:10',
                'color' => 'nullable|string|max:7',
            ]);

            // Cek duplikat nama untuk user ini
            $exists = Category::where('user_id', Auth::id())
                ->where('name', $validated['name'])
                ->where('type', $validated['type'])
                ->where('id', '!=', $category->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'A category with this name already exists for this type'
                ], 409);
            }

            $category->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $category->fresh()
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus kategori
     */
    public function destroy($id)
    {
        try {
            $category = Category::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            if ($category->transactions()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category that has transactions'
                ], 409);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil kategori income user
     */
    public function getIncomeCategories()
    {
        try {
            $categories = Category::where('user_id', Auth::id())
                ->where('type', 'income')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get income categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil kategori expense user
     */
    public function getExpenseCategories()
    {
        try {
            $categories = Category::where('user_id', Auth::id())
                ->where('type', 'expense')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get expense categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

