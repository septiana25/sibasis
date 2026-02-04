<?php

namespace App\Services;

use App\Models\Bookmark;
use App\Models\SearchStatistic;
use Illuminate\Support\Facades\DB;

class DashboardService
{

  public function getBookmarkByMember(int $member_id)
  {
    return Bookmark::where('member_id', $member_id)->get();
  }

  public function getStatisticByMember(int $member_id, int $limit = 5)
  {
    return SearchStatistic::select('nim', 'judul_ta', DB::raw('count(*) as total'))
      ->groupBy('nim', 'judul_ta')
      ->orderByDesc('total')
      ->where('member_id', $member_id)
      ->limit($limit)
      ->get();
    //return SearchStatistic::where('member_id', $member_id)->get();

  }
}
