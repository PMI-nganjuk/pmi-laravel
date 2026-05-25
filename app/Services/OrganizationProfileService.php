<?php

namespace App\Services;

use App\Models\OrganizationProfile;
use App\Repositories\OrganizationProfileRepository;
use Illuminate\Support\Facades\DB;

class OrganizationProfileService
{
    public function __construct(
        protected OrganizationProfileRepository $repository
    ) {}

    /**
     * Get the active organization profile.
     */
    public function getProfile(): OrganizationProfile
    {
        return $this->repository->firstOrCreate();
    }

    /**
     * Update the organization profile details.
     */
    public function updateProfile(array $data): OrganizationProfile
    {
        return DB::transaction(function () use ($data) {
            $profile = $this->repository->firstOrCreate();
            return $this->repository->update($profile, $data);
        });
    }
}
