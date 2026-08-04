<?php

namespace App\Repositories;

use App\Models\OrganizationProfile;

class OrganizationProfileRepository
{
    /**
     * Get the first profile configuration, or create a new empty one.
     */
    public function firstOrCreate(): OrganizationProfile
    {
        $profile = OrganizationProfile::orderBy('fiscal_year', 'desc')->first();
        if ($profile) {
            return $profile;
        }

        return OrganizationProfile::firstOrCreate(
            ['id' => 1],
            [
                'organization_name' => null,
                'address' => null,
                'chairperson' => null,
                'headquarters_treasurer' => null,
                'blood_donation_unit_treasurer' => null,
                'financial_period_start' => null,
                'financial_period_end' => null,
                'fiscal_year' => null,
            ]
        );
    }

    /**
     * Update the organization profile.
     */
    public function update(OrganizationProfile $profile, array $attributes): OrganizationProfile
    {
        $profile->update($attributes);
        return $profile;
    }
}
