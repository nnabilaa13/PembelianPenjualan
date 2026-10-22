<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    /**
     * Get purchase datatables list.
     */
    public function data(Request $request): JsonResponse
    {
        $session_user = $request->session()->get('user');
        $session_user_obj = is_array($session_user) ? (object) $session_user : $session_user;
        $result = Purchase::getDatatablesData($request->all(), $session_user_obj);

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
     * Get single purchase detail or datatables.
     */
    public function get(Request $request, ?int $id = null): JsonResponse
    {
        if ($id !== null) {
            $result = Purchase::getPurchaseDetail($id);

            return response()->json($result['body'], $result['status']);
        }

        return $this->data($request);
    }

    /**
     * Store new purchase transaction.
     */
    public function post(Request $request): JsonResponse
    {
        $session_user = $request->session()->get('user');
        $session_user_obj = is_array($session_user) ? (object) $session_user : $session_user;
        $result = Purchase::storePurchase($request->all(), $session_user_obj);

        return response()->json($result['body'], $result['status']);
    }

    /**
     * Update purchase transaction.
     */
    public function put(Request $request, int $id): JsonResponse
    {
        $session_user = $request->session()->get('user');
        $session_user_obj = is_array($session_user) ? (object) $session_user : $session_user;
        $result = Purchase::updatePurchase($id, $request->all(), $session_user_obj);

        return response()->json($result['body'], $result['status']);
    }

    /**
     * Update purchase transaction via PATCH.
     */
    public function patch(Request $request, int $id): JsonResponse
    {
        return $this->put($request, $id);
    }

    /**
     * Delete purchase transaction.
     */
    public function delete(Request $request, int $id): JsonResponse
    {
        $session_user = $request->session()->get('user');
        $session_user_obj = is_array($session_user) ? (object) $session_user : $session_user;
        $result = Purchase::deletePurchase($id, $session_user_obj);

        return response()->json($result['body'], $result['status']);
    }
}
