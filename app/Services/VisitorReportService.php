<?php

namespace App\Services;

use App\Models\SearchStatistic;
use Illuminate\Support\Facades\DB;

class VisitorReportService
{
    public function getPaginatedVisitor(int $perPage = 15, array $filters = [])
    {
        $query = SearchStatistic::query()->with('memberType')
            ->select([
                'member_id',
                DB::raw('ANY_VALUE(member_name) as member_name'),
                DB::raw('ANY_VALUE(member_type_id) as member_type_id'),
                DB::raw('ANY_VALUE(nim) as nim'),
                DB::raw('ANY_VALUE(nama) as nama'),
                DB::raw('COUNT(*) as total_visits'),
                DB::raw('MAX(DATE(created_at)) as last_visit'),
            ]);

        // Apply search filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('member_name', 'like', "%{$search}%")
                    ->orWhere('member_id', 'like', "%{$search}%");
            });
        }

        // Apply date range filter (default: current month)
        $dateFrom = !empty($filters['date_from']) ? $filters['date_from'] : now()->startOfMonth()->toDateString();
        $dateTo   = !empty($filters['date_to'])   ? $filters['date_to']   : now()->endOfMonth()->toDateString();

        $query->whereDate('created_at', '>=', $dateFrom)
              ->whereDate('created_at', '<=', $dateTo);

        $query->groupByRaw('member_id, DATE(created_at)');
        $query->orderBy('last_visit', 'desc');

        return $query->paginate($perPage);
    }
}