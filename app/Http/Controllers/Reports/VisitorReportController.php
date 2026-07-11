<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Services\VisitorReportService;
use Illuminate\Http\Request;

class VisitorReportController extends Controller
{
    public function __construct(
        protected VisitorReportService $visitorReportService
    ) {}
    public function index(Request $request)
    {
        $filters = $this->buildFilters($request);
        $perPage = $request->get('per_page', 15);

        $visitors = $this->visitorReportService->getPaginatedVisitor($perPage, $filters);

        // Preserve query parameters in pagination links
        $visitors->appends($request->query());

        return view('reports.visitor', [
            'title' => 'Laporan Pengunjung',
            'active' => 'reports.visitor',
            'visitors' => $visitors,
            'filters' => $filters,
        ]);
    }
    /* {

        return view('reports.visitor', [
            'title' => 'Laporan Pengunjung',
            'active' => 'reports.visitor',
            'visitors' => $this->visitorReportService->getPaginatedVisitor()
        ]);
    } */

    public function export(Request $request)
    {
        $filters = $this->buildFilters($request);

        return $this->visitorReportService->exportToExcel($filters);
    }

    /**
     * Build filters array from request parameters
     */
    protected function buildFilters(Request $request): array
    {
        return [
            'search' => $request->get('search'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];
    }
}
