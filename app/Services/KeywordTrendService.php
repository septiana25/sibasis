<?php

namespace App\Services;

use App\Models\SearchStatistic;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KeywordTrendService
{
    public function getPaginatedKeywords(int $perPage = 15, array $filters = [])
    {
        $query = SearchStatistic::query()
            ->select([
                'nim',
                DB::raw('ANY_VALUE(judul_ta) as judul_ta'),
                DB::raw('ANY_VALUE(nama) as nama'),
                DB::raw('ANY_VALUE(program_studi) as program_studi'),
                DB::raw('ANY_VALUE(fakultas) as fakultas'),
                DB::raw('COUNT(*) as total_searches'),
                DB::raw('MAX(DATE(created_at)) as last_searched'),
            ]);

        $this->applyFilters($query, $filters);

        $query->groupBy('nim')
              ->orderByDesc('total_searches');

        return $query->paginate($perPage);
    }

    public function exportToExcel(array $filters = []): StreamedResponse
    {
        $filename = 'tren_kata_kunci_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($filters) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM so Excel opens with correct encoding
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['#', 'NIM', 'Nama', 'Judul TA', 'Program Studi', 'Fakultas', 'Total Pencarian', 'Terakhir Dicari']);

            $query = SearchStatistic::query()
                ->select([
                    'nim',
                    DB::raw('ANY_VALUE(judul_ta) as judul_ta'),
                    DB::raw('ANY_VALUE(nama) as nama'),
                    DB::raw('ANY_VALUE(program_studi) as program_studi'),
                    DB::raw('ANY_VALUE(fakultas) as fakultas'),
                    DB::raw('COUNT(*) as total_searches'),
                    DB::raw('MAX(DATE(created_at)) as last_searched'),
                ]);

            $this->applyFilters($query, $filters);

            $query->groupBy('nim')
                  ->orderByDesc('total_searches');

            $no = 1;
            $query->chunk(500, function ($rows) use ($handle, &$no) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $no++,
                        $row->nim,
                        $row->nama,
                        $row->judul_ta,
                        $row->program_studi,
                        $row->fakultas,
                        $row->total_searches,
                        $row->last_searched
                            ? \Carbon\Carbon::parse($row->last_searched)->format('d-m-Y')
                            : 'N/A',
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function applyFilters($query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('judul_ta', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['fakultas'])) {
            $query->where('fakultas', $filters['fakultas']);
        }

        if (!empty($filters['program_studi'])) {
            $query->where('program_studi', 'like', '%' . $filters['program_studi'] . '%');
        }

        $dateFrom = !empty($filters['date_from']) ? $filters['date_from'] : now()->startOfMonth()->toDateString();
        $dateTo   = !empty($filters['date_to'])   ? $filters['date_to']   : now()->endOfMonth()->toDateString();

        $query->whereDate('created_at', '>=', $dateFrom)
              ->whereDate('created_at', '<=', $dateTo);
    }
}
