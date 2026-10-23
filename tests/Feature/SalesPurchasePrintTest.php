<?php

namespace Tests\Feature;

use App\Models\Inventories;
use App\Models\PurchaseDetails;
use App\Models\Purchases;
use App\Models\Sales;
use App\Models\SalesDetails;
use App\Models\Users;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesPurchasePrintTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_print_renders_the_selected_transaction_and_print_action(): void
    {
        $sales_user = Users::create([
            'name' => 'Sales Print Clerk',
            'role' => 'Sales',
        ]);
        $selected_inventory = Inventories::create([
            'code' => 'PRINT-SALE-001',
            'name' => 'Selected sale item',
            'price' => 1250,
            'stock' => 8,
        ]);
        $other_inventory = Inventories::create([
            'code' => 'PRINT-SALE-002',
            'name' => 'Other sale item',
            'price' => 3000,
            'stock' => 5,
        ]);
        $selected_sale = Sales::create([
            'number' => 'INV-PRINT-001',
            'date' => '2026-10-23',
            'user_id' => $sales_user->id,
        ]);
        SalesDetails::create([
            'sales_id' => $selected_sale->id,
            'inventory_id' => $selected_inventory->id,
            'qty' => 2,
            'price' => 1250,
        ]);
        $other_sale = Sales::create([
            'number' => 'INV-PRINT-002',
            'date' => '2026-10-22',
            'user_id' => $sales_user->id,
        ]);
        SalesDetails::create([
            'sales_id' => $other_sale->id,
            'inventory_id' => $other_inventory->id,
            'qty' => 1,
            'price' => 3000,
        ]);

        $response = $this->withSession(['user' => ['id' => $sales_user->id, 'role' => 'Sales']])
            ->get(route('sales.print', $selected_sale->id));

        $response->assertOk()
            ->assertSee('INV-PRINT-001')
            ->assertSee('23/10/2026')
            ->assertSee('Sales Print Clerk')
            ->assertSee('PRINT-SALE-001')
            ->assertSee('Selected sale item')
            ->assertSee('>2<', false)
            ->assertSee('Rp 1.250')
            ->assertSee('Rp 2.500')
            ->assertDontSee('INV-PRINT-002')
            ->assertDontSee('Other sale item')
            ->assertSee('window.print()', false);

        $data_response = $this->withSession(['user' => ['id' => $sales_user->id, 'role' => 'Sales']])
            ->getJson(route('sales.data'));

        $selected_row = collect($data_response->json('data'))->firstWhere('id', $selected_sale->id);
        $this->assertStringContainsString(route('sales.print', $selected_sale->id), $selected_row['action']);
    }

    public function test_purchase_print_renders_the_selected_transaction_and_print_action(): void
    {
        $purchase_user = Users::create([
            'name' => 'Purchase Print Clerk',
            'role' => 'Purchase',
        ]);
        $selected_inventory = Inventories::create([
            'code' => 'PRINT-PURCHASE-001',
            'name' => 'Selected purchase item',
            'price' => 2250,
            'stock' => 8,
        ]);
        $other_inventory = Inventories::create([
            'code' => 'PRINT-PURCHASE-002',
            'name' => 'Other purchase item',
            'price' => 5000,
            'stock' => 5,
        ]);
        $selected_purchase = Purchases::create([
            'number' => 'PO-PRINT-001',
            'date' => '2026-10-23',
            'user_id' => $purchase_user->id,
        ]);
        PurchaseDetails::create([
            'purchase_id' => $selected_purchase->id,
            'inventory_id' => $selected_inventory->id,
            'qty' => 3,
            'price' => 2250,
        ]);
        $other_purchase = Purchases::create([
            'number' => 'PO-PRINT-002',
            'date' => '2026-10-22',
            'user_id' => $purchase_user->id,
        ]);
        PurchaseDetails::create([
            'purchase_id' => $other_purchase->id,
            'inventory_id' => $other_inventory->id,
            'qty' => 1,
            'price' => 5000,
        ]);

        $response = $this->withSession(['user' => ['id' => $purchase_user->id, 'role' => 'Purchase']])
            ->get(route('purchases.print', $selected_purchase->id));

        $response->assertOk()
            ->assertSee('PO-PRINT-001')
            ->assertSee('23/10/2026')
            ->assertSee('Purchase Print Clerk')
            ->assertSee('PRINT-PURCHASE-001')
            ->assertSee('Selected purchase item')
            ->assertSee('>3<', false)
            ->assertSee('Rp 2.250')
            ->assertSee('Rp 6.750')
            ->assertDontSee('PO-PRINT-002')
            ->assertDontSee('Other purchase item')
            ->assertSee('window.print()', false);

        $data_response = $this->withSession(['user' => ['id' => $purchase_user->id, 'role' => 'Purchase']])
            ->getJson(route('purchases.data'));

        $selected_row = collect($data_response->json('data'))->firstWhere('id', $selected_purchase->id);
        $this->assertStringContainsString(route('purchases.print', $selected_purchase->id), $selected_row['action']);
    }

    public function test_sales_and_purchase_print_return_not_found_for_missing_transactions(): void
    {
        $session_user = ['id' => 1, 'role' => 'SuperAdmin'];

        $this->withSession(['user' => $session_user])
            ->get(route('sales.print', 999))
            ->assertNotFound();

        $this->withSession(['user' => $session_user])
            ->get(route('purchases.print', 999))
            ->assertNotFound();
    }
}
