<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Requests\Review\UpdateReviewRequest;
use App\Repositories\Review\ReviewRepositoryInterface;
use App\Http\Traits\ApiResponseTrait;

class ReviewController extends Controller
{
    public function __construct(
        protected ReviewRepositoryInterface $reviewRepository
    ) {}

    /** USER: create review */
    public function store(StoreReviewRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $review = $this->reviewRepository->store($data);

        return response()->json($review, 201);
    }

    /** USER: view own reviews */
    public function myReviews(Request $request)
    {
        return $this->reviewRepository->getByUser($request->user()->id);
    }

    /** PUBLIC: reviews by product */
    public function productReviews(int $productId)
    {
        return $this->reviewRepository->getByProduct($productId);
    }

    /** ADMIN: all reviews */
    public function index()
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        return $this->reviewRepository->getAll();
    }

    /** ADMIN: update review */
    public function update(UpdateReviewRequest $request, Review $review)
    {
        $this->authorize('update', $review);

        return $this->reviewRepository->update(
            $review,
            $request->validated()
        );
    }

    /** USER / ADMIN: delete review */
    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);

        $this->reviewRepository->delete($review);
        return $this->successResponse(
                "",
                'Review deleted successfully.'
            );
    }
}
