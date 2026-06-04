<?php

namespace App\Services;

use App\Models\OrganizationProfile;
use App\Repositories\OrganizationProfileRepository;
use Illuminate\Support\Facades\Cache;
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
        $raw = Cache::remember('organization.profile', now()->addHour(), function () {
            return $this->repository->firstOrCreate()->getAttributes();
        });

        return (new OrganizationProfile)->newFromBuilder($raw);
    }

    /**
     * Update the organization profile details.
     */
    public function updateProfile(array $data): OrganizationProfile
    {
        return DB::transaction(function () use ($data) {
            $profile = $this->repository->firstOrCreate();
            $updated = $this->repository->update($profile, $data);
            Cache::forget('organization.profile');
            return $updated;
        });
    }
}
