<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Category\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Traits\HandlesImagesTrait;
use Illuminate\Support\Str;
use App\Models\Category;

class CategoryController extends Controller
{
    use ApiResponseTrait, HandlesImagesTrait;
        public function __construct(protected CategoryRepositoryInterface $categoryRepository) {}

        public function index(Request $request)
    {
        try {
            $perPage = (int) $request->input('pageSize', 10);
            $search = $request->input('searchValue', '');
            $sort = $request->input('sort', 'desc');
            $sortColumn = $request->input('column', 'id');

            $data = $this->categoryRepository->index($search, $perPage, $sort, $sortColumn);
            return $this->successResponse($data);
        } catch (\Exception $e) {
            Log::error('Error getting Park:', ['error' => $e->getMessage()]);
            return $this->errorResponse('An unexpected error occurred. Please try again later.');
        }
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
            $data = $request->validated();
            $data['image'] = $request->hasFile('image')
                ? $this->handleImageUpload($request, 'image', 'uploads/category')
                : null;
            $data['slug'] = Str::slug($data['name']);
            $category = $this->categoryRepository->store($data);
            return $this->successResponse($category, 'Category created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating Category:', ['error' => $e->getMessage()]);
            return $this->errorResponse('An unexpected error occurred. Please try again later.');
        }
    }

    public function update(StoreCategoryRequest $request,Category $category)
    {
        try {
            $data = $request->validated();
            if ($request->hasFile('image')) {
                if ($category->image) {
                    $this->deleteImage('', $category->image);
                }
                $data['image'] = $this->handleImageUpload($request, 'image', 'uploads/category');
            }
            $data['slug'] = Str::slug($data['name']);
            $this->categoryRepository->update($category, $data);
            return $this->successResponse($category, 'Category updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating Category:', ['error' => $e->getMessage()]);
            return $this->errorResponse('An unexpected error occurred. Please try again later.');
        }
    }

        public function show(Category $category)
    {
        return $this->successResponse(
            $this->categoryRepository->find($category)
        );
    }

    public function destroy(Category $category)
    {
        try {
            if (!empty($category['image'])) {
                $this->deleteImage('', $category->image);
            }
            $this->categoryRepository->delete($category);
            return $this->successResponse("", 'Category deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting Category:', ['error' => $e->getMessage()]);
            return $this->errorResponse('An unexpected error occurred. Please try again later.');
        }
    }
}
