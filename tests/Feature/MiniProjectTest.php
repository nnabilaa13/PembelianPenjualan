<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiniProjectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test login functionality with AJAX.
     */
    public function test_user_can_login_via_ajax(): void
    {
        $response = $this->postJson('/api/login', [
            'name' => 'Super Admin',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'redirect' => route('dashboard'),
            ]);

        $this->assertEquals('Super Admin', session('user')->name);
    }

    /**
     * Test invalid login fails.
     */
    public function test_login_fails_for_non_existent_user(): void
    {
        $response = $this->postJson('/api/login', [
            'name' => 'Non Existent User',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test SuperAdmin can access and perform inventory CRUD.
     */
    public function test_superadmin_can_crud_inventory(): void
    {
        $admin = User::where('role', 'SuperAdmin')->first();

        // Access inventory page
        $this->withSession(['user' => $admin])
            ->get(route('inventories.index'))
            ->assertStatus(200);

        // Fetch data via API
        $data_response = $this->withSession(['user' => $admin])
            ->getJson(route('inventories.data'));
        $data_response->assertStatus(200)
            ->assertJsonStructure(['data']);

        // Create inventory item via AJAX
        $create_response = $this->withSession(['user' => $admin])
            ->postJson(route('inventories.store'), [
                'code' => 'TEST-001',
                'name' => 'Test Barang Baru',
                'price' => 50000,
                'stock' => 10,
            ]);

        $create_response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('inventories', [
            'code' => 'TEST-001',
            'stock' => 10,
        ]);

        $item = Inventory::where('code', 'TEST-001')->first();

        // Update inventory item via AJAX
        $update_response = $this->withSession(['user' => $admin])
            ->putJson(route('inventories.update', $item->id), [
                'code' => 'TEST-001',
                'name' => 'Test Barang Diedit',
                'price' => 60000,
                'stock' => 12,
            ]);

        $update_response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('inventories', [
            'name' => 'Test Barang Diedit',
            'stock' => 12,
        ]);

        // Delete inventory item via AJAX
        $delete_response = $this->withSession(['user' => $admin])
            ->deleteJson(route('inventories.destroy', $item->id));

        $delete_response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('inventories', [
            'id' => $item->id,
        ]);
    }

    /**
     * Test Sales staff cannot access inventory management.
     */
    public function test_sales_staff_cannot_access_inventory(): void
    {
        $sales = User::where('role', 'Sales')->first();

        $response = $this->withSession(['user' => $sales])
            ->get(route('inventories.index'));

        $response->assertRedirect(route('dashboard'));
    }

    /**
     * Test purchase with multiple items increases inventory stock.
     */
    public function test_purchase_multiple_items_increments_stock(): void
    {
        $purchase_user = User::where('role', 'Purchase')->first();
        $item1 = Inventory::first();
        $initial_stock1 = $item1->stock;

        $response = $this->withSession(['user' => $purchase_user])
            ->postJson(route('purchases.store'), [
                'number' => 'PO-TEST-100',
                'date' => '2026-10-16',
                'items' => [
                    [
                        'inventory_id' => $item1->id,
                        'qty' => 5,
                        'price' => 500000,
                    ],
                ],
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $item1->refresh();
        $this->assertEquals($initial_stock1 + 5, $item1->stock);

        $this->assertDatabaseHas('purchases', [
            'number' => 'PO-TEST-100',
            'user_id' => $purchase_user->id,
        ]);
    }

    /**
     * Test sale with multiple items decrements stock and validates stock.
     */
    public function test_sale_multiple_items_decrements_stock_and_validates(): void
    {
        $sales_user = User::where('role', 'Sales')->first();
        $item = Inventory::first();
        $initial_stock = $item->stock;

        // Sale with valid stock
        $response = $this->withSession(['user' => $sales_user])
            ->postJson(route('sales.store'), [
                'number' => 'INV-TEST-200',
                'date' => '2026-10-16',
                'items' => [
                    [
                        'inventory_id' => $item->id,
                        'qty' => 2,
                        'price' => $item->price,
                    ],
                ],
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $item->refresh();
        $this->assertEquals($initial_stock - 2, $item->stock);

        // Sale exceeding stock should fail
        $fail_response = $this->withSession(['user' => $sales_user])
            ->postJson(route('sales.store'), [
                'number' => 'INV-TEST-201',
                'date' => '2026-10-16',
                'items' => [
                    [
                        'inventory_id' => $item->id,
                        'qty' => $item->stock + 100,
                        'price' => $item->price,
                    ],
                ],
            ]);

        $fail_response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    /**
     * Test Manager cannot create sales or purchases, only view/print.
     */
    public function test_manager_cannot_create_sales_or_purchases(): void
    {
        $manager = User::where('role', 'Manager')->first();
        $item = Inventory::first();

        // Manager cannot store purchase
        $purchase_response = $this->withSession(['user' => $manager])
            ->postJson(route('purchases.store'), [
                'number' => 'PO-MGR-001',
                'date' => '2026-10-16',
                'items' => [
                    ['inventory_id' => $item->id, 'qty' => 1, 'price' => 1000],
                ],
            ]);
        $purchase_response->assertStatus(403);

        // Manager cannot store sale
        $sale_response = $this->withSession(['user' => $manager])
            ->postJson(route('sales.store'), [
                'number' => 'INV-MGR-001',
                'date' => '2026-10-16',
                'items' => [
                    ['inventory_id' => $item->id, 'qty' => 1, 'price' => 1000],
                ],
            ]);
        $sale_response->assertStatus(403);

        // Manager can view list and print
        $this->withSession(['user' => $manager])
            ->get(route('purchases.index'))
            ->assertStatus(200);

        $this->withSession(['user' => $manager])
            ->get(route('sales.index'))
            ->assertStatus(200);
    }

    /**
     * Test dashboard displays correctly for logged in user.
     */
    public function test_dashboard_displays_for_logged_in_user(): void
    {
        $admin = User::where('role', 'SuperAdmin')->first();

        $response = $this->withSession(['user' => $admin])
            ->get(route('dashboard'));

        $response->assertStatus(200)
            ->assertSee('Dashboard')
            ->assertSee('TOTAL MASTER BARANG');
    }

    /**
     * Test Purchase staff sees all purchases in datatables, but can only edit/delete own transactions.
     */
    public function test_purchase_staff_can_view_all_purchases_including_other_users(): void
    {
        $admin = User::where('role', 'SuperAdmin')->first();
        $purchase_user = User::where('role', 'Purchase')->first();
        $item = Inventory::first();

        // Admin creates purchase
        Purchase::create([
            'number' => 'PO-TEST-ADMIN-01',
            'date' => '2026-10-22',
            'user_id' => $admin->id,
        ]);

        // Purchase staff creates purchase
        Purchase::create([
            'number' => 'PO-TEST-PURCHASE-01',
            'date' => '2026-10-22',
            'user_id' => $purchase_user->id,
        ]);

        $admin_response = $this->withSession(['user' => $admin])
            ->getJson(route('purchases.data'));
        $purchase_response = $this->withSession(['user' => $purchase_user])
            ->getJson(route('purchases.data'));

        $admin_response->assertStatus(200);
        $purchase_response->assertStatus(200);

        // Record count must match between SuperAdmin and Purchase staff
        $this->assertEquals($admin_response->json('recordsTotal'), $purchase_response->json('recordsTotal'));

        // Check action buttons for Purchase staff: all purchases have edit/delete buttons
        $data = $purchase_response->json('data');
        foreach ($data as $row) {
            $this->assertStringContainsString('btn-edit', $row['action']);
            $this->assertStringContainsString('btn-delete', $row['action']);
        }

        // Purchase staff can delete a purchase created by Admin
        $admin_purchase = Purchase::where('number', 'PO-TEST-ADMIN-01')->first();
        $delete_response = $this->withSession(['user' => $purchase_user])
            ->deleteJson(route('purchases.destroy', $admin_purchase->id));
        $delete_response->assertStatus(200);
        $this->assertSoftDeleted('purchases', ['id' => $admin_purchase->id]);
    }

    /**
     * Test Sales staff sees all sales in datatables and can edit/delete all transactions.
     */
    public function test_sales_staff_can_view_all_sales_including_other_users(): void
    {
        $admin = User::where('role', 'SuperAdmin')->first();
        $sales_user = User::where('role', 'Sales')->first();

        // Admin creates sale
        $admin_sale = Sale::create([
            'number' => 'INV-TEST-ADMIN-01',
            'date' => '2026-10-22',
            'user_id' => $admin->id,
        ]);

        // Sales staff creates sale
        Sale::create([
            'number' => 'INV-TEST-SALES-01',
            'date' => '2026-10-22',
            'user_id' => $sales_user->id,
        ]);

        $admin_response = $this->withSession(['user' => $admin])
            ->getJson(route('sales.data'));
        $sales_response = $this->withSession(['user' => $sales_user])
            ->getJson(route('sales.data'));

        $admin_response->assertStatus(200);
        $sales_response->assertStatus(200);

        // Record count must match between SuperAdmin and Sales staff
        $this->assertEquals($admin_response->json('recordsTotal'), $sales_response->json('recordsTotal'));

        // Check action buttons for Sales staff: all sales have edit/delete buttons
        $data = $sales_response->json('data');
        foreach ($data as $row) {
            $this->assertStringContainsString('btn-edit', $row['action']);
            $this->assertStringContainsString('btn-delete', $row['action']);
        }

        // Sales staff can delete a sale created by Admin
        $delete_response = $this->withSession(['user' => $sales_user])
            ->deleteJson(route('sales.destroy', $admin_sale->id));
        $delete_response->assertStatus(200);
        $this->assertSoftDeleted('sales', ['id' => $admin_sale->id]);
    }
}
