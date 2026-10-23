<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Purchases;
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
        $result = Purchases::getPurchaseDetail($id);

        if (! $result['body']['success']) {
            abort($result['status']);
        }

        return view('purchase.print', ['purchase' => $result['body']['data']]);
    }
}
