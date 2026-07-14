<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Traits\ApiResponseTrait;
use App\policies\AddressPolicy;
use App\Http\Traits\ResolvesIndexFiltersTrait;
use App\Http\Requests\Address\StoreAddressRequest;
use App\Repositories\Address\AddressRepositoryInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AddressController extends Controller
{
    use ApiResponseTrait, ResolvesIndexFiltersTrait, AuthorizesRequests;

    public function __construct(
        protected AddressRepositoryInterface $addressRepository
    ) {}

    /**
     * ADMIN: List all addresses
     */
    public function listALL(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $filters = $this->getIndexFilters($request);

        $addresses = $this->addressRepository->index(
            userId: null,
            search: $filters['search'],
            perPage: $filters['perPage'],
            sort: $filters['sort'],
            sortColumn: $filters['column']
        );

        return $this->successResponse($addresses);
    }

    /**
     * USER: List own addresses
     */
    public function index(Request $request)
    {
        $filters = $this->getIndexFilters($request);

        $addresses = $this->addressRepository->index(
            userId: $request->user()->id,
            search: $filters['search'],
            perPage: $filters['perPage'],
            sort: $filters['sort'],
            sortColumn: $filters['column']
        );

        return $this->successResponse($addresses);
    }

    /**
     * Create address
     */
    public function store(StoreAddressRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $address = $this->addressRepository->store($data);

        return $this->successResponse(
            $address,
            'Address created successfully.'
        );
    }

    /**
     * View address
     */
    public function show(Address $address)
    {
        $this->authorize('view', $address);

        return $this->successResponse($address);
    }

    /**
     * Update address
     */
    public function update(StoreAddressRequest $request, Address $address)
    {
        $this->authorize('update', $address);

        $this->addressRepository->update($address, $request->validated());

        return $this->successResponse(
            $address->refresh(),
            'Address updated successfully.'
        );
    }

    /**
     * Delete address
     */
    public function destroy(Address $address)
    {
        $this->authorize('delete', $address);

        $this->addressRepository->delete($address);

        return $this->successResponse(
            '',
            'Address deleted successfully.'
        );
    }
}
