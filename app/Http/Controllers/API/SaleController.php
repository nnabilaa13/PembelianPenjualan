<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    /**
     * Get sales datatables list.
     */
    public function data(Request $request): JsonResponse
    {
        $session_user = $request->session()->get('user');
        $session_user_obj = is_array($session_user) ? (object) $session_user : $session_user;
        $result = Sale::getDatatablesData($request->all(), $session_user_obj);

        return response()->json($result);
    }

    /**
     * Alias for datatables request.
     */
    public function datatables(Request $request): JsonResponse
    {
        return $this->data($request);
    }

    /**
     * Get single sale detail or datatables.
     */
    public function get(Request $request, ?int $id = null): JsonResponse
    {
        if ($id !== null) {
            $result = Sale::getSaleDetail($id);

            return response()->json($result['body'], $result['status']);
        }

        return $this->data($request);
    }

    /**
     * Store new sale transaction.
     */
    public function post(Request $request): JsonResponse
    {
        $session_user = $request->session()->get('user');
        $session_user_obj = is_array($session_user) ? (object) $session_user : $session_user;
        $result = Sale::storeSale($request->all(), $session_user_obj);

        return response()->json($result['body'], $result['status']);
    }

    /**
     * Update sale transaction.
     */
    public function put(Request $request, int $id): JsonResponse
    {
        $session_user = $request->session()->get('user');
        $session_user_obj = is_array($session_user) ? (object) $session_user : $session_user;
        $result = Sale::updateSale($id, $request->all(), $session_user_obj);

        return response()->json($result['body'], $result['status']);
    }

    /**
     * Update sale transaction via PATCH.
     */
    public function patch(Request $request, int $id): JsonResponse
    {
        return $this->put($request, $id);
    }

    /**
     * Delete sale transaction.
     */
    public function delete(Request $request, int $id): JsonResponse
    {
        $session_user = $request->session()->get('user');
        $session_user_obj = is_array($session_user) ? (object) $session_user : $session_user;
        $result = Sale::deleteSale($id, $session_user_obj);

        return response()->json($result['body'], $result['status']);
    }
}
