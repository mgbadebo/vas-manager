<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\{VasRevenue, Aggregator, Mno, Service, PartnerShareSummary};

class ReportsController extends Controller
{
    public function revenue(Request $r)
    {
        $start = Carbon::now()->startOfYear();
        $end   = Carbon::now();

        $byAggregator = DB::table('vas_revenues as vr')
            ->join('aggregators as ag', 'vr.aggregator_id', '=', 'ag.id')
            ->leftJoin('partner_share_summaries as ps', 'ps.vas_revenue_id', '=', 'vr.id')
            ->whereBetween('vr.payment_date', [$start, $end])
            ->selectRaw('ag.name as aggregator, SUM(vr.gross_revenue_a) as A,
                         COALESCE(SUM(ps.ra_after_mandatory),0) as RA,
                         COALESCE(SUM(ps.rs_share_pool),0) as RS')
            ->groupBy('ag.name')->orderByDesc('A')->get();

        $byMNO = DB::table('vas_revenues as vr')
            ->join('mnos as m', 'vr.mno_id', '=', 'm.id')
            ->leftJoin('partner_share_summaries as ps', 'ps.vas_revenue_id', '=', 'vr.id')
            ->whereBetween('vr.payment_date', [$start, $end])
            ->selectRaw('m.name as mno, SUM(vr.gross_revenue_a) as A,
                         COALESCE(SUM(ps.ra_after_mandatory),0) as RA,
                         COALESCE(SUM(ps.rs_share_pool),0) as RS')
            ->groupBy('m.name')->orderByDesc('A')->get();

        $byService = DB::table('vas_revenues as vr')
            ->join('services as s', 'vr.service_id', '=', 's.id')
            ->leftJoin('partner_share_summaries as ps', 'ps.vas_revenue_id', '=', 'vr.id')
            ->whereBetween('vr.payment_date', [$start, $end])
            ->selectRaw('s.name as service, SUM(vr.gross_revenue_a) as A,
                         COALESCE(SUM(ps.ra_after_mandatory),0) as RA,
                         COALESCE(SUM(ps.rs_share_pool),0) as RS')
            ->groupBy('s.name')->orderByDesc('A')->get();

        $list = VasRevenue::with(['service','mno','aggregator','partnerShareSummary'])
            ->whereBetween('payment_date', [$start, $end])
            ->orderByDesc('payment_date')->paginate(25);

        return view('reports.revenue', compact('byAggregator','byMNO','byService','list','start','end'));
    }

    public function services()
    {
        $rows = Service::with('serviceType')->orderBy('name')->get();
        return view('reports.services', compact('rows'));
    }

    public function mnos()
    {
        $rows = Mno::orderBy('name')->get();
        return view('reports.mnos', compact('rows'));
    }

    public function aggregators()
    {
        $rows = Aggregator::orderBy('name')->get();
        return view('reports.aggregators', compact('rows'));
    }
}
