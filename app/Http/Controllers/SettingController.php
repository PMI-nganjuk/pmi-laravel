<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationProfileRequest;
use App\Services\OrganizationProfileService;
use Illuminate\Support\Facades\Gate;

class SettingController extends Controller
{
    public function __construct(
        protected OrganizationProfileService $profileService
    ) {}

    /**
     * Show the system settings page.
     */
    public function index()
    {
        Gate::authorize('manage-settings');

        $profile = $this->profileService->getProfile();

        return view('pages.settings', compact('profile'));
    }

    /**
     * Update the organization profile settings.
     */
    public function update(OrganizationProfileRequest $request)
    {
        Gate::authorize('manage-settings');

        $this->profileService->updateProfile($request->validated());

        return redirect()->route('settings.index')
            ->with('success', 'Profil organisasi berhasil diperbarui.');
    }
}
