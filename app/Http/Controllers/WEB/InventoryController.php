<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class InventoryController extends Controller
{
    /**
     * Display the inventory index page.
     */
    public function index(): View
    {
        return view('data_master.inventory.index');
    }
}
