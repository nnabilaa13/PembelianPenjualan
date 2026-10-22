<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\View\View;

class SaleController extends Controller
{
    /**
     * Display a listing of sales.
     */
    public function index(): View
    {
        return view('sales.index');
    }

    /**
     * Display the print view for a sale receipt.
     */
    public function print(int $id): View
    {
        $sale = Sale::with('details')->findOrFail($id);
        $sale_data = $sale->toDetailArray();

        return view('sales.print', [
            'sale' => $sale_data,
        ]);
    }
}
