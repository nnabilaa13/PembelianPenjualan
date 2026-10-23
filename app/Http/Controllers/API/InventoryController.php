<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Inventories;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function get(Request $request, $id = null)
    {
        $params = $request->all();

        if ($id != null) {
            $result = Inventories::getItemDetail((int) $id);

            return response()->json($result);
        } elseif (isset($params['all']) && $params['all']) {
            $res = Inventories::getAllResult($params, $request);
        } else {
            $res = Inventories::getPaginatedResult($params, $request);
        }

        return $res;
    }

    public function post(Request $request)
    {
        $params = $request->all();

        $session_user = $request->session()->get('user');
        $session_user = is_array($session_user) ? (object) $session_user : $session_user;

        $result = Inventories::storeItem($params);

        return response()->json($result['body'], $result['status']);
    }

    public function put(Request $request, $id)
    {
        $params = $request->all();

        $result = Inventories::updateItem((int) $id, $params);

        return response()->json($result['body'], $result['status']);
    }

    public function patch(Request $request, $id)
    {
        $params = $request->all();

        $result = Inventories::updateItem((int) $id, $params);

        return response()->json($result['body'], $result['status']);
    }

    public function delete(Request $request, $id)
    {
        $result = Inventories::deleteItem((int) $id);

        return response()->json($result['body'], $result['status']);
    }

    public function approve(Request $request, $id)
    {
        $params = $request->all();

        return Inventories::approveById($id, $params, $request);
    }

    public function data(Request $request)
    {
        $result = Inventories::getDataList();

        return response()->json($result);
    }

    public function select2(Request $request)
    {
        $search_term = $request->get('q') ?? $request->get('search');

        $result = Inventories::getSelect2Data($search_term);

        return response()->json($result);
    }

    public function datatables(Request $request)
    {
        $session_user = $request->session()->get('user');
        $session_user = is_array($session_user) ? (object) $session_user : $session_user;

        $result = Inventories::getDatatablesData($request->all());

        return response()->json($result);
    }
}
