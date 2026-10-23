<?php

namespace Tests\Feature;

use App\Models\Inventories;
use App\Models\PurchaseDetails;
use App\Models\SalesDetails;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesPurchaseDetailUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_detail_update_create_and_delete_work_through_the_api(): void
    {
        $session_user = ['id' => 1, 'role' => 'Sales'];
        $inventory = Inventories::create([
            'code' => 'SALES-001',
            'name' => 'Sales test item',
            'price' => 100,
            'stock' => 10,
        ]);

        $create_response = $this->withSession(['user' => $session_user])
            ->postJson(route('sales.store'), [
                'number' => 'INV-TEST-001',
                'date' => '2026-10-23',
                'items' => [
                    [
                        'inventory_id' => $inventory->id,
                        'qty' => 2,
                        'price' => 100,
                    ],
                ],
            ]);

        $create_response->assertOk()->assertJsonPath('success', true);
        $sale_id = $create_response->json('data.id');
        $this->assertSame(8, $inventory->fresh()->stock);

        $detail_response = $this->withSession(['user' => $session_user])
            ->getJson(route('get.sales', ['id' => $sale_id]));

        $detail_response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $sale_id)
            ->assertJsonPath('data.items.0.inventory_id', $inventory->id)
            ->assertJsonPath('data.items.0.qty', 2);

        $update_response = $this->withSession(['user' => $session_user])
            ->putJson(route('sales.update', ['id' => $sale_id]), [
                'number' => 'INV-TEST-001-UPDATED',
                'date' => '2026-10-24',
                'items' => [
                    [
                        'inventory_id' => $inventory->id,
                        'qty' => 4,
                        'price' => 125,
                    ],
                ],
            ]);

        $update_response->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('sales', [
            'id' => $sale_id,
            'number' => 'INV-TEST-001-UPDATED',
        ]);
        $this->withSession(['user' => $session_user])
            ->getJson(route('get.sales', ['id' => $sale_id]))
            ->assertOk()
            ->assertJsonPath('data.date', '2026-10-24');
        $this->assertSame(1, SalesDetails::where('sales_id', $sale_id)->count());
        $this->assertSame(4, SalesDetails::where('sales_id', $sale_id)->value('qty'));
        $this->assertSame(125.0, (float) SalesDetails::where('sales_id', $sale_id)->value('price'));
        $this->assertSame(6, $inventory->fresh()->stock);

        $this->withSession(['user' => $session_user])
            ->deleteJson(route('sales.destroy', ['id' => $sale_id]))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSoftDeleted('sales', ['id' => $sale_id]);
        $this->assertSame(10, $inventory->fresh()->stock);
    }

    public function test_purchase_detail_update_create_and_delete_work_through_the_api(): void
    {
        $session_user = ['id' => 1, 'role' => 'Purchase'];
        $inventory = Inventories::create([
            'code' => 'PURCHASE-001',
            'name' => 'Purchase test item',
            'price' => 100,
            'stock' => 10,
        ]);

        $create_response = $this->withSession(['user' => $session_user])
            ->postJson(route('purchases.store'), [
                'number' => 'PO-TEST-001',
                'date' => '2026-10-23',
                'items' => [
                    [
                        'inventory_id' => $inventory->id,
                        'qty' => 2,
                        'price' => 100,
                    ],
                ],
            ]);

        $create_response->assertOk()->assertJsonPath('success', true);
        $purchase_id = $create_response->json('data.id');
        $this->assertSame(12, $inventory->fresh()->stock);

        $detail_response = $this->withSession(['user' => $session_user])
            ->getJson(route('get.purchases', ['id' => $purchase_id]));

        $detail_response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $purchase_id)
            ->assertJsonPath('data.items.0.inventory_id', $inventory->id)
            ->assertJsonPath('data.items.0.qty', 2);

        $update_response = $this->withSession(['user' => $session_user])
            ->putJson(route('purchases.update', ['id' => $purchase_id]), [
                'number' => 'PO-TEST-001-UPDATED',
                'date' => '2026-10-24',
                'items' => [
                    [
                        'inventory_id' => $inventory->id,
                        'qty' => 4,
                        'price' => 125,
                    ],
                ],
            ]);

        $update_response->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('purchases', [
            'id' => $purchase_id,
            'number' => 'PO-TEST-001-UPDATED',
        ]);
        $this->withSession(['user' => $session_user])
            ->getJson(route('get.purchases', ['id' => $purchase_id]))
            ->assertOk()
            ->assertJsonPath('data.date', '2026-10-24');
        $this->assertSame(1, PurchaseDetails::where('purchase_id', $purchase_id)->count());
        $this->assertSame(4, PurchaseDetails::where('purchase_id', $purchase_id)->value('qty'));
        $this->assertSame(125.0, (float) PurchaseDetails::where('purchase_id', $purchase_id)->value('price'));
        $this->assertSame(14, $inventory->fresh()->stock);

        $this->withSession(['user' => $session_user])
            ->deleteJson(route('purchases.destroy', ['id' => $purchase_id]))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSoftDeleted('purchases', ['id' => $purchase_id]);
        $this->assertSame(10, $inventory->fresh()->stock);
    }

    public function test_sales_and_purchase_detail_endpoints_return_a_not_found_response(): void
    {
        $session_user = ['id' => 1, 'role' => 'SuperAdmin'];

        $this->withSession(['user' => $session_user])
            ->getJson(route('get.sales', ['id' => 999]))
            ->assertNotFound()
            ->assertJsonPath('success', false);

        $this->withSession(['user' => $session_user])
            ->getJson(route('get.purchases', ['id' => 999]))
            ->assertNotFound()
            ->assertJsonPath('success', false);
    }
}
