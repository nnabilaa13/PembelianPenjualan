<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'role',
    ];

    /**
     * Get the sales created by the user.
     *
     * @return HasMany<Sales, $this>
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sales::class, 'user_id');
    }

    /**
     * Get the purchases created by the user.
     *
     * @return HasMany<Purchases, $this>
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchases::class, 'user_id');
    }

    /**
     * Fetch all metrics required for the dashboard.
     *
     * @return array<string, mixed>
     */
    public static function getDashboardMetrics(object $session_user): array
    {
        $total_inventories = Inventories::count();
        $total_stock = Inventories::sum('stock');
        $low_stock_count = Inventories::where('stock', '<=', 10)->count();

        $purchases_query = Purchases::query();
        $total_purchases_count = $purchases_query->count();

        $total_purchase_amount = (float) DB::table('purchase_details')
            ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
            ->sum(DB::raw('purchase_details.qty * purchase_details.price'));

        $sales_query = Sales::query();
        $total_sales_count = $sales_query->count();

        $total_sale_amount = (float) DB::table('sales_details')
            ->join('sales', 'sales_details.sales_id', '=', 'sales.id')
            ->sum(DB::raw('sales_details.qty * sales_details.price'));

        $recent_sales = Sales::with('details')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        $recent_purchases = Purchases::with('details')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        return [
            'user' => $session_user,
            'total_inventories' => $total_inventories,
            'total_stock' => $total_stock,
            'low_stock_count' => $low_stock_count,
            'total_purchases_count' => $total_purchases_count,
            'total_purchase_amount' => $total_purchase_amount,
            'total_sales_count' => $total_sales_count,
            'total_sale_amount' => $total_sale_amount,
            'recent_sales' => $recent_sales,
            'recent_purchases' => $recent_purchases,
        ];
    }
}
