<?php

namespace Tests\Feature;

use App\Models\Inventories;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_detail_action_and_endpoint_use_the_selected_item_id(): void
    {
        $session_user = ['id' => 1, 'role' => 'SuperAdmin'];
        $first_item = Inventories::create([
            'code' => 'DETAIL-001',
            'name' => 'Detail test item one',
            'price' => 12500,
            'stock' => 7,
        ]);
        $second_item = Inventories::create([
            'code' => 'DETAIL-002',
            'name' => 'Detail test item two',
            'price' => 25000,
            'stock' => 14,
        ]);

        $this->withSession(['user' => $session_user])
            ->get(route('inventories.index'))
            ->assertOk()
            ->assertSee('modal-inventory_detail')
            ->assertSee('detail-inventory_id');

        $table_response = $this->withSession(['user' => $session_user])
            ->postJson(route('datatable.inventories'), [
                'draw' => 1,
                'start' => 0,
                'length' => 10,
            ]);

        $table_response->assertOk();
        $table_data = collect($table_response->json('data'))->keyBy('id');
        $this->assertStringContainsString('btn-detail" data-id="'.$first_item->id.'"', $table_data[$first_item->id]['action']);
        $this->assertStringContainsString('btn-detail" data-id="'.$second_item->id.'"', $table_data[$second_item->id]['action']);

        $first_detail_response = $this->withSession(['user' => $session_user])
            ->getJson(route('get.inventories', ['id' => $first_item->id]));
        $first_detail_response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $first_item->id)
            ->assertJsonPath('data.code', 'DETAIL-001')
            ->assertJsonPath('data.name', 'Detail test item one');

        $second_detail_response = $this->withSession(['user' => $session_user])
            ->getJson(route('get.inventories', ['id' => $second_item->id]));
        $second_detail_response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $second_item->id)
            ->assertJsonPath('data.code', 'DETAIL-002')
            ->assertJsonPath('data.name', 'Detail test item two');

        $this->assertNotSame(
            $first_detail_response->json('data.id'),
            $second_detail_response->json('data.id')
        );
    }
}
