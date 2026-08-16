<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationProfileRequest;
use App\Models\User;
use App\Services\OrganizationProfileService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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
        $userNames = User::orderBy('name')->pluck('name');

        return view('pages.settings', compact('profile', 'userNames'));
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

    /**
     * Download the manual book (.pdf).
     */
    public function downloadManualBook()
    {
        $filePath = public_path('manual-book/Manual_Book_PMI_Nganjuk.pdf');

        if (!file_exists($filePath)) {
            $filePath = base_path('Manual_Book_PMI_Nganjuk.pdf');
        }

        if (!file_exists($filePath)) {
            abort(404, 'Dokumen manual book tidak ditemukan.');
        }

        return response()->download($filePath, 'Manual_Book_PMI_Nganjuk.pdf', [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Download the manual book (.docx).
     */
    public function downloadManualBookDocx()
    {
        $filePath = public_path('manual-book/Manual_Book_PMI_Nganjuk.docx');

        if (!file_exists($filePath)) {
            abort(404, 'Dokumen manual book (.docx) tidak ditemukan.');
        }

        return response()->download($filePath, 'Manual_Book_PMI_Nganjuk.docx', [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Update manual book files and Google Drive link.
     */
    public function updateManualBook(Request $request)
    {
        Gate::authorize('manage-settings');

        $request->validate([
            'manual_book_link' => ['nullable', 'url', 'max:1000'],
            'manual_book_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'], // max 10MB
            'manual_book_docx' => ['nullable', 'file', 'mimes:docx,doc', 'max:10240'], // max 10MB
        ]);

        $profile = $this->profileService->getProfile();

        // Update Google Drive link
        $this->profileService->updateProfile([
            'manual_book_link' => $request->input('manual_book_link')
        ]);

        $uploadPath = public_path('manual-book');
        
        // Buat folder jika belum ada
        if (!File::isDirectory($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true, true);
        }

        // Handle PDF upload
        if ($request->hasFile('manual_book_pdf')) {
            $pdfFile = $request->file('manual_book_pdf');
            $pdfName = 'Manual_Book_PMI_Nganjuk.pdf';
            
            // Delete old file if exists
            if (File::exists($uploadPath . '/' . $pdfName)) {
                File::delete($uploadPath . '/' . $pdfName);
            }
            
            $pdfFile->move($uploadPath, $pdfName);
        }

        // Handle DOCX upload
        if ($request->hasFile('manual_book_docx')) {
            $docxFile = $request->file('manual_book_docx');
            $docxName = 'Manual_Book_PMI_Nganjuk.docx';
            
            // Delete old file if exists
            if (File::exists($uploadPath . '/' . $docxName)) {
                File::delete($uploadPath . '/' . $docxName);
            }
            
            $docxFile->move($uploadPath, $docxName);
        }

        return redirect()->route('settings.index')
            ->with('success', 'Manual Book berhasil diperbarui.');
    }
}



