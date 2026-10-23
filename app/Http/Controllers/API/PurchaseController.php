<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Purchases;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function get(Request $request, $id = null)
    {
        $params = $request->all();

        if ($id != null) {
            $result = Purchases::getPurchaseDetail((int) $id);

            return response()->json($result['body'], $result['status']);
        } elseif (isset($params['all']) && $params['all']) {
            $res = Purchases::getAllResult($params, $request);
        } else {
            $res = Purchases::getPaginatedResult($params, $request);
        }

        return $res;
    }

    public function post(Request $request)
    {
        $params = $request->all();

        $session_user = $request->session()->get('user');
        $session_user = is_array($session_user) ? (object) $session_user : $session_user;

        $result = Purchases::storePurchase($params, $session_user);

        return response()->json($result['body'], $result['status']);
    }

    public function put(Request $request, $id)
    {
        $params = $request->all();

        $session_user = $request->session()->get('user');
        $session_user = is_array($session_user) ? (object) $session_user : $session_user;

        $result = Purchases::updatePurchase((int) $id, $params, $session_user);

        return response()->json($result['body'], $result['status']);
    }

    public function patch(Request $request, $id)
    {
        $params = $request->all();

        $session_user = $request->session()->get('user');
        $session_user = is_array($session_user) ? (object) $session_user : $session_user;

        $result = Purchases::updatePurchase((int) $id, $params, $session_user);

        return response()->json($result['body'], $result['status']);
    }

    public function delete(Request $request, $id)
    {
        $session_user = $request->session()->get('user');
        $session_user = is_array($session_user) ? (object) $session_user : $session_user;

        $result = Purchases::deletePurchase((int) $id, $session_user);

        return response()->json($result['body'], $result['status']);
    }

    public function approve(Request $request, $id)
    {
        $params = $request->all();

        return Purchases::approveById($id, $params, $request);
    }

    public function data(Request $request)
    {
        $session_user = $request->session()->get('user');
        $session_user = is_array($session_user) ? (object) $session_user : $session_user;

        $result = Purchases::getDatatablesData($request->all(), $session_user);

        return response()->json($result);
    }

    public function datatables(Request $request)
    {
        $session_user = $request->session()->get('user');
        $session_user = is_array($session_user) ? (object) $session_user : $session_user;

        $result = Purchases::getDatatablesData($request->all(), $session_user);

        return response()->json($result);
    }
}
