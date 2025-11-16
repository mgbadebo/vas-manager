<?php

namespace App\Http\Controllers;

use App\Models\Aggregator;
use App\Models\Mno;
use App\Models\PartnerShareSummary;
use App\Models\Service;
use App\Models\VasRevenue;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(): View
    {
        $today = Carbon::today();
        $startYear = Carbon::now()->startOfYear();

        $lastRevenue = VasRevenue::with(['aggregator', 'service', 'mno', 'partnerShareSummary'])
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->first();

        $ytdRevenueQuery = VasRevenue::query()
            ->whereNotNull('payment_date')
            ->whereBetween('payment_date', [$startYear, $today]);

        $A_ytd = (clone $ytdRevenueQuery)->sum('gross_revenue_a');
        $ytdRevenueIds = (clone $ytdRevenueQuery)->pluck('id');

        $summaryYtd = PartnerShareSummary::whereIn('vas_revenue_id', $ytdRevenueIds)
            ->selectRaw('
                COALESCE(SUM(mandatory_total_me), 0) as me,
                COALESCE(SUM(ra_after_mandatory), 0) as ra,
                COALESCE(SUM(operational_total_oe), 0) as oe,
                COALESCE(SUM(rs_share_pool), 0) as rs
            ')
            ->first();

        $RA_ytd = $summaryYtd->ra ?? 0;
        $RS_ytd = $summaryYtd->rs ?? 0;

        $countServices = Service::count();
        $countMNOs = Mno::count();
        $countAggregators = Aggregator::count();

        $rsByMonth = PartnerShareSummary::whereIn('vas_revenue_id', $ytdRevenueIds)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as ym, SUM(rs_share_pool) as rs')
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $labels = $rsByMonth->pluck('ym')->toArray();
        $rsSeries = $rsByMonth->pluck('rs')->map(fn ($value) => (float) $value)->toArray();

        return view('dashboard.index', compact(
            'lastRevenue',
            'A_ytd',
            'RA_ytd',
            'RS_ytd',
            'countServices',
            'countMNOs',
            'countAggregators',
            'labels',
            'rsSeries'
        ));
    }
}
