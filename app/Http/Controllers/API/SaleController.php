<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sales;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function get(Request $request, $id = null)
    {
        $params = $request->all();

        if ($id != null) {
            $result = Sales::getSaleDetail((int) $id);

            return response()->json($result['body'], $result['status']);
        } elseif (isset($params['all']) && $params['all']) {
            $res = Sales::getAllResult($params, $request);
        } else {
            $res = Sales::getPaginatedResult($params, $request);
        }

        return $res;
    }

    public function post(Request $request)
    {
        $params = $request->all();

        $session_user = $request->session()->get('user');
        $session_user = is_array($session_user) ? (object) $session_user : $session_user;

        $result = Sales::storeSale($params, $session_user);

        return response()->json($result['body'], $result['status']);
    }

    public function put(Request $request, $id)
    {
        $params = $request->all();

        $session_user = $request->session()->get('user');
        $session_user = is_array($session_user) ? (object) $session_user : $session_user;

        $result = Sales::updateSale((int) $id, $params, $session_user);

        return response()->json($result['body'], $result['status']);
    }

    public function patch(Request $request, $id)
    {
        $params = $request->all();

        $session_user = $request->session()->get('user');
        $session_user = is_array($session_user) ? (object) $session_user : $session_user;

        $result = Sales::updateSale((int) $id, $params, $session_user);

        return response()->json($result['body'], $result['status']);
    }

    public function delete(Request $request, $id)
    {
        $session_user = $request->session()->get('user');
        $session_user = is_array($session_user) ? (object) $session_user : $session_user;

        $result = Sales::deleteSale((int) $id, $session_user);

        return response()->json($result['body'], $result['status']);
    }

    public function approve(Request $request, $id)
    {
        $params = $request->all();

        return Sales::approveById($id, $params, $request);
    }

    public function data(Request $request)
    {
        $session_user = $request->session()->get('user');
        $session_user = is_array($session_user) ? (object) $session_user : $session_user;

        $result = Sales::getDatatablesData($request->all(), $session_user);

        return response()->json($result);
    }

    public function datatables(Request $request)
    {
        $session_user = $request->session()->get('user');
        $session_user = is_array($session_user) ? (object) $session_user : $session_user;

        $result = Sales::getDatatablesData($request->all(), $session_user);

        return response()->json($result);
    }
}
