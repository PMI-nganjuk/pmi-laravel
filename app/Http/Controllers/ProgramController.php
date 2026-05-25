<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProgramRequest;
use App\Models\Program;
use App\Services\ProgramService;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function __construct(
        protected ProgramService $service
    ) {}

    /**
     * Display a listing of the programs.
     */
    public function index(Request $request)
    {
        return view('pages.programs', $this->service->getPageData($request->query()));
    }

    /**
     * Store a newly created program in storage.
     */
    public function store(StoreProgramRequest $request)
    {
        try {
            $this->service->store($request->validated());

            return redirect()->route('programs.index')->with('success', 'Program kerja berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan program kerja: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified program in storage.
     */
    public function update(StoreProgramRequest $request, Program $program)
    {
        try {
            $this->service->update($program, $request->validated());

            return redirect()->route('programs.index')->with('success', 'Program kerja berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui program kerja: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified program from storage.
     */
    public function destroy(Program $program)
    {
        try {
            $this->service->delete($program);

            return redirect()->route('programs.index')->with('success', 'Program kerja berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus program kerja: ' . $e->getMessage());
        }
    }
}
