<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class FranchiseGetOrderDataOfYear extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // Validate selected_year in request
        $validated = $request->validate([
            'selected_year' => 'required|integer|digits:4', // Ensure it's a valid 4-digit year
        ]);

        // Get the authenticated franchise's ID
        $franchiseId = auth('franchise')->id();

        // Get the selected year and the last year
        $selectedYear = $validated['selected_year'];
        $lastYear = $selectedYear - 1;

        // Get the order data for the selected year and the previous year
        $orderData = $this->getOrderDataByYear($franchiseId, $selectedYear);
        $orderDataLastYear = $this->getOrderDataByYear($franchiseId, $lastYear);

        // Return the response with the order data
        return response()->json([
            'status'    => true,
            'msg'       => 'Success',
            'data'      => array_values($orderData),
            'last_year' => array_values($orderDataLastYear),
        ]);
    }

    /**
     * Get order data for a specific year grouped by month.
     */
    private function getOrderDataByYear(int $franchiseId, int $year): array
    {
        return Order::orderBy('created_at')
            ->whereYear('created_at', $year)
            ->whereNull('deleted_at')
            ->where('franchise_id', $franchiseId)
            ->get()
            ->groupBy(function ($date) {
                return $date->created_at->month;
            })
            ->map(function ($group) {
                return $group->count();
            })
            ->union(array_fill(1, 12, 0))
            ->sortKeys()
            ->toArray();
    }
}
