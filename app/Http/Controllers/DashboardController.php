<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private ReportService $reportService,
        private StockService $stockService,
    ) {}

    public function index(Request $request): View
    {
        $today = $this->reportService->dailySales(today()->toDateString());
        $month = $this->reportService->monthlySales(now()->year, now()->month);
        $lowStock = $this->stockService->getLowStockProducts();
        $topProducts = $this->reportService->topProducts(
            now()->subDays(30)->toDateString(),
            now()->toDateString(),
            5,
        );

        return view('dashboard', compact('today', 'month', 'lowStock', 'topProducts'));
    }
}
