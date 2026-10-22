<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Get inventory items list for API / tests.
     */
    public function data(): JsonResponse
    {
        $result = Inventory::getDataList();

        return response()->json($result);
    }

    /**
     * Get select2 formatted options for dropdowns.
     */
    public function select2(Request $request): JsonResponse
    {
        $search_term = $request->query('q');
        $result = Inventory::getSelect2Data($search_term);

        return response()->json($result);
    }

    /**
     * Get inventory items for jQuery DataTables.
     */
    public function datatables(Request $request): JsonResponse
    {
        $result = Inventory::getDatatablesData($request->all());

        return response()->json($result);
    }

    /**
     * Get single inventory item or list.
     */
    public function get(Request $request, ?int $id = null): JsonResponse
    {
        if ($id !== null) {
            $result = Inventory::getItemDetail($id);

            return response()->json($result, $result['success'] ? 200 : 404);
        }

        return $this->data();
    }

    /**
     * Store new inventory item.
     */
    public function post(Request $request): JsonResponse
    {
        $result = Inventory::storeItem($request->all());

        return response()->json($result['body'], $result['status']);
    }

    /**
     * Update existing inventory item.
     */
    public function put(Request $request, int $id): JsonResponse
    {
        $result = Inventory::updateItem($id, $request->all());

        return response()->json($result['body'], $result['status']);
    }

    /**
     * Update existing inventory item via PATCH.
     */
    public function patch(Request $request, int $id): JsonResponse
    {
        return $this->put($request, $id);
    }

    /**
     * Delete inventory item.
     */
    public function delete(Request $request, int $id): JsonResponse
    {
        $result = Inventory::deleteItem($id);

        return response()->json($result['body'], $result['status']);
    }
}
