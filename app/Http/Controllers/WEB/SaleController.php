<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Sales;
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
        $result = Sales::getSaleDetail($id);

        if (! $result['body']['success']) {
            abort($result['status']);
        }

        return view('sales.print', ['sale' => $result['body']['data']]);
    }
}
