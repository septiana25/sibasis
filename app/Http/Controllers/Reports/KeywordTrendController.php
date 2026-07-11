<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Services\KeywordTrendService;
use Illuminate\Http\Request;

class KeywordTrendController extends Controller
{
    public function __construct(
        protected KeywordTrendService $keywordTrendService
    ) {}

    public function index(Request $request)
    {
        $filters = $this->buildFilters($request);
        $perPage = $request->get('per_page', 15);

        $keywords = $this->keywordTrendService->getPaginatedKeywords($perPage, $filters);

        $keywords->appends($request->query());

        return view('reports.keyword', [
            'title'    => 'Tren Kata Kunci Pencarian',
            'active'   => 'reports.keyword',
            'keywords' => $keywords,
            'filters'  => $filters,
        ]);
    }

    public function export(Request $request)
    {
        $filters = $this->buildFilters($request);

        return $this->keywordTrendService->exportToExcel($filters);
    }

    protected function buildFilters(Request $request): array
    {
        return [
            'search'       => $request->get('search'),
            'date_from'    => $request->get('date_from'),
            'date_to'      => $request->get('date_to'),
            'fakultas'     => $request->get('fakultas'),
            'program_studi'=> $request->get('program_studi'),
        ];
    }
}
