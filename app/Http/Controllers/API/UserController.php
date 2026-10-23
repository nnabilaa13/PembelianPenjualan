<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function get(Request $request, $id = null)
    {
        $params = $request->all();

        if ($id != null) {
            $res = Users::getById($id, $params, $request);
        } elseif (isset($params['all']) && $params['all']) {
            $res = Users::getAllResult($params, $request);
        } else {
            $res = Users::getPaginatedResult($params, $request);
        }

        return $res;
    }

    public function post(Request $request)
    {
        $params = $request->all();

        return Users::createOrUpdate($params, $request->method(), $request);
    }

    public function put(Request $request, $id)
    {
        $params = $request->all();
        $params['id'] = $id;

        return Users::createOrUpdate($params, $request->method(), $request);
    }

    public function patch(Request $request, $id)
    {
        $params = $request->all();
        $params['id'] = $id;

        return Users::createOrUpdate($params, $request->method(), $request);
    }

    public function delete(Request $request, $id)
    {
        $params = $request->all();

        return Users::deleteById($id, $params, $request);
    }

    public function approve(Request $request, $id)
    {
        $params = $request->all();

        return Users::approveById($id, $params, $request);
    }

    public function datatables(Request $request)
    {
        $user = auth()->guard('sanctum')->user();

        $columns = [
            'users.id',
        ];

        $dataOrder = [];

        $limit = $request->length;

        $start = $request->start;

        foreach ($request->order as $row) {
            $nestedOrder['column'] = $columns[$row['column']];
            $nestedOrder['dir'] = $row['dir'];

            $dataOrder[] = $nestedOrder;
        }

        $order = $dataOrder;

        $dir = $request->order[0]['dir'];

        $search = $request->search['value'];

        $filter = $request->filter;

        $res = Users::datatables($start, $limit, $order, $dir, $search, $filter);

        $data = [];

        if (! empty($res['data'])) {
            foreach ($res['data'] as $row) {
                $nestedData = $row;
                $nestedData['action'] = '';
                $nestedData['action'] .= '<div class="dropdown">';
                $nestedData['action'] .= '    <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Aksi</button>';
                $nestedData['action'] .= '    <div class="dropdown-menu dropdown-menu-animated">';
                $nestedData['action'] .= '        <a href="javascript:void(0);" class="dropdown-item" id="edit-data" data-id="'.$row['id'].'">Edit</a>';
                $nestedData['action'] .= '        <a href="javascript:void(0);" class="dropdown-item" id="delete-data" data-id="'.$row['id'].'">Delete</a>';
                $nestedData['action'] .= '    </div>';
                $nestedData['action'] .= '</div>';

                $data[] = $nestedData;
            }
        }

        $json_data = [
            'draw' => intval($request->draw),
            'recordsTotal' => intval($res['total_data']),
            'recordsFiltered' => intval($res['total_filtered']),
            'data' => $data,
            'order' => $order,
        ];

        return json_encode($json_data);
    }
}
