<?php

namespace App\Http\Controllers;

use App\Services\GeneralLedgerService;
use Illuminate\Http\Request;

class GeneralLedgerController extends Controller
{
    public function __construct(
        protected GeneralLedgerService $service
    ) {}

    /**
     * Render the general ledger report page.
     */
    public function index(Request $request)
    {
        return view('pages.general-ledger', $this->service->getPageData($request->query()));
    }
}
