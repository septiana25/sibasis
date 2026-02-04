<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class MemberDashboardController extends Controller
{

    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $member = Auth::guard('member')->user();
        $bookmarks = $this->dashboardService->getBookmarkByMember($member->member_id);
        $statistic = $this->dashboardService->getStatisticByMember($member->member_id);
        return view('member.dashboard', ['name' => $member->member_name, 'bookmarks' => $bookmarks, 'statistic' => $statistic]);
    }
}
