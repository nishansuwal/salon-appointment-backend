<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Traits\HandlesImagesTrait;
use App\Http\Traits\ResolvesIndexFiltersTrait;
use App\Models\Category;
use App\Repositories\Category\CategoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    use ApiResponseTrait, HandlesImagesTrait, ResolvesIndexFiltersTrait;

    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository
    ) {}

    public function getCategoriesByLevelForClient($level)
    {
        try {
            $categories = $this->categoryRepository->getCategoriesByLevelForClient($level);

            return $this->successResponse($categories);
        } catch (\Exception $e) {
            Log::error('Error getting categories by level for client:', ['error' => $e->getMessage()]);

            return $this->errorResponse('An unexpected error occurred. Please try again later.');
        }
    }

    /**
     * Display a listing of categories.
     */
    public function index(Request $request)
    {
        try {
            $filters = $this->getIndexFilters($request);

            $categories = $this->categoryRepository->index($filters);

            return $this->successResponse($categories);
        } catch (\Throwable $e) {
            Log::error('Error fetching categories.', [
                'message' => $e->getMessage(),
            ]);

            return $this->errorResponse(
                'An unexpected error occurred. Please try again later.'
            );
        }
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $this->handleImageUpload(
                    $request,
                    'image',
                    'uploads/category'
                );
            }

            $category = $this->categoryRepository->store($data);

            return $this->successResponse(
                $category,
                'Category created successfully.'
            );
        } catch (\Throwable $e) {
            Log::error('Error creating category.', [
                'message' => $e->getMessage(),
            ]);

            return $this->errorResponse(
                'An unexpected error occurred. Please try again later.'
            );
        }
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        try {
            return $this->successResponse(
                $this->categoryRepository->find($category)
            );
        } catch (\Throwable $e) {
            Log::error('Error fetching category.', [
                'message' => $e->getMessage(),
            ]);

            return $this->errorResponse(
                'An unexpected error occurred. Please try again later.'
            );
        }
    }

    /**
     * Update the specified category.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('image')) {

                if ($category->image) {
                    $this->deleteImage('', $category->image);
                }

                $data['image'] = $this->handleImageUpload(
                    $request,
                    'image',
                    'uploads/category'
                );
            }

            $category = $this->categoryRepository->update(
                $category,
                $data
            );

            return $this->successResponse(
                $category,
                'Category updated successfully.'
            );
        } catch (\Throwable $e) {
            Log::error('Error updating category.', [
                'message' => $e->getMessage(),
            ]);

            return $this->errorResponse(
                'An unexpected error occurred. Please try again later.'
            );
        }
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category)
    {
        try {
            if ($category->image) {
                $this->deleteImage('', $category->image);
            }

            $this->categoryRepository->delete($category);

            return $this->successResponse(
                '',
                'Category deleted successfully.'
            );
        } catch (\Throwable $e) {
            Log::error('Error deleting category.', [
                'message' => $e->getMessage(),
            ]);

            return $this->errorResponse(
                'An unexpected error occurred. Please try again later.'
            );
        }
    }
}
