<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    /**
     * Display a listing of purchases.
     */
    public function index(): View
    {
        return view('purchase.index');
    }

    /**
     * Display the print view for a purchase receipt.
     */
    public function print(int $id): View
    {
        $purchase = Purchase::with('details')->findOrFail($id);
        $purchase_data = $purchase->toDetailArray();

        return view('purchase.print', [
            'purchase' => $purchase_data,
        ]);
    }
}
