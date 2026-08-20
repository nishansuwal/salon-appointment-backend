<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Requests\Review\UpdateReviewRequest;
use App\Repositories\Review\ReviewRepositoryInterface;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Traits\ResolvesIndexFiltersTrait;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    use ApiResponseTrait, ResolvesIndexFiltersTrait;
    public function __construct(
        protected ReviewRepositoryInterface $reviewRepository
    ) {}

    /** USER: create review */
    public function store(StoreReviewRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['status'] = 'pending';

        $review = $this->reviewRepository->store($data);

        return response()->json($review, 201);
    }

    /** USER: view own reviews */
    public function myReviews(Request $request)
    {
        $perPage = (int) $request->input('pageSize', 10);
        $status = $request->input('status', 'approved');
        $request->validate([
            'status' => 'nullable|in:pending,approved,rejected',
        ]);
        return $this->reviewRepository->getByUser($request->user()->id, $perPage, $status);
    }

    /** PUBLIC: reviews by product */
    public function serviceReviews(int $serviceId)
    {
        return $this->reviewRepository->getByService($serviceId);
    }

    /** ADMIN: all reviews */
    public function index(Request $request)
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        $filters = $this->getIndexFilters($request);
        return $this->reviewRepository->getAll($filters);
    }

    /** ADMIN: update review */
    public function update(StoreReviewRequest $request, Review $review)
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

    public function staffReviews(Request $request)
    {
        $staffId = Auth::user()->staff->id;
        $perPage = (int) $request->input('pageSize', 10);
        return $this->reviewRepository->getByStaff($staffId, $perPage);
    }

    public function updateStatus(Request $request, Review $review)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        return $this->reviewRepository->update(
            $review,
            ['status' => $request->input('status'), 'admin_note' => $request->input('admin_note')]
        );
    }

    public function reviewStatus(Request $request, int $serviceId)
    {
        $userId = Auth::id();
        $apointmentService = $this->reviewRepository->reviewStatus($serviceId, $userId);

        return $this->successResponse([
            'can_review' => $apointmentService !== null,
            'appointment_item_id' => $apointmentService?->id,
        ]);
    }
}
