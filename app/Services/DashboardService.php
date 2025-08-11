<?php
namespace App\Services;

use App\Models\Bookmark;
use App\Models\SearchStatistic;
use Illuminate\Support\Facades\DB;

class DashboardService{

  public function getBookmarkByMember(int $member_id)
  {
    return Bookmark::where('member_id', $member_id)->get();

  }

  public function getStatisticByMember(int $member_id, int $limit = 5)
  {
    return SearchStatistic::select('judul_ta', 'program_studi', DB::raw('count(*) as total'))
            ->groupBy('judul_ta', 'program_studi')
            ->orderByDesc('total')
            ->where('member_id', $member_id)
            ->limit($limit)
            ->get();
    //return SearchStatistic::where('member_id', $member_id)->get();

  }
}