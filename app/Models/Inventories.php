<?php

namespace App\Models;

use App\Helpers\ModelHelper;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string code
 * @property string name
 * @property int    price
 * @property int    stock
 * @property int    deleted_at
 * @property int    created_at
 * @property int    updated_at
 */
class Inventories extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'inventories';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'price',
        'stock',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'code' => 'string', 'name' => 'string', 'price' => 'int', 'stock' => 'int', 'deleted_at' => 'datetime', 'created_at' => 'datetime', 'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [

    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    public $incrementing = true;

    // Scopes...

    // Functions ...

    // Relations ...

    public static function mapSchema($params = [], $user = [])
    {
        $model = new self;

        return [
            'field' => [
                'id' => ['column' => $model->table.'.id', 'alias' => 'id', 'type' => 'int'],
                'code' => ['column' => $model->table.'.code', 'alias' => 'code', 'type' => 'string'],
                'name' => ['column' => $model->table.'.name', 'alias' => 'name', 'type' => 'string'],
                'price' => ['column' => $model->table.'.price', 'alias' => 'price', 'type' => 'int'],
                'stock' => ['column' => $model->table.'.stock', 'alias' => 'stock', 'type' => 'int'],
                'deleted_at' => ['column' => $model->table.'.deleted_at', 'alias' => 'deleted_at', 'type' => 'date'],
                'created_at' => ['column' => $model->table.'.created_at', 'alias' => 'created_at', 'type' => 'date'],
                'updated_at' => ['column' => $model->table.'.updated_at', 'alias' => 'updated_at', 'type' => 'date'],
            ],
            'join' => [

            ],
            'where' => [

            ],
        ];
    }

    public static function datatables($start, $length, $order, $dir, $search, $filter = [])
    {
        $schema = self::mapSchema();

        $totalData = self::count();

        $qry = ModelHelper::select($schema['field'], null, __CLASS__);
        ModelHelper::join($schema['join'], null, $qry);

        // FILTER

        $totalFiltered = $qry->count();

        if (empty($search)) {

            if ($length > 0) {
                $qry->skip($start)
                    ->take($length);
            }

            foreach ($order as $row) {
                $qry->orderBy($row['column'], $row['dir']);
            }

        } else {
            foreach (array_values($schema['field']) as $key => $val) {
                if ($key < 1) {
                    $qry->whereRaw('('.$val['column'].'::varchar(255) ILIKE \'%'.$search.'%\'');
                } elseif (count(array_values($schema['field'])) == ($key + 1)) {
                    $qry->orWhereRaw($val['column'].'::varchar(255) ILIKE \'%'.$search.'%\')');
                } else {
                    $qry->orWhereRaw($val['column'].'::varchar(255) ILIKE \'%'.$search.'%\'');
                }
            }

            $totalFiltered = $qry->count();

            if ($length > 0) {
                $qry->skip($start)
                    ->take($length);
            }

            foreach ($order as $row) {
                $qry->orderBy($row['column'], $row['dir']);
            }
        }

        return [
            'data' => $qry->get(),
            'totalData' => $totalData,
            'totalFiltered' => $totalFiltered,
        ];
    }

    public static function getPaginatedResult($params, $request)
    {
        $append = [];
        $schema = self::mapSchema();

        $paramsPage = isset($params['page']) ? $params['page'] : 0;

        $or = [];

        unset($params['page']);

        if (isset($params['or']) && $params['or']) {
            $or = $params['or'];
            unset($params['or']);
        }

        $db = ModelHelper::select($schema['field'], $request, __CLASS__);
        ModelHelper::join($schema['join'], $request, $db);

        if ($params) {
            ModelHelper::dynamicFilterAnd($params, $request, $db, __CLASS__);
        }

        if ($or) {
            ModelHelper::dynamicFilterOr($or, $request, $db, __CLASS__);
        }

        $results = ModelHelper::generatePagingResults($schema, $paramsPage, $params, $request, $db, $append);

        return response()->json($results);
    }

    public static function getById($id, $params = [], $request = null)
    {
        $models = new self;

        $append = [];

        $schema = self::mapSchema();

        $db = ModelHelper::select($schema['field'], $request, __CLASS__)->where($models->table.'.id', $id);

        ModelHelper::join($schema['join'], $request, $db);

        return response()->json($db->first());
    }

    public static function getAllResult($params, $request)
    {
        $append = [];
        $schema = self::mapSchema();

        $or = [];

        unset($params['all']);

        if (isset($params['or']) && $params['or']) {
            $or = $params['or'];
            unset($params['or']);
        }

        $db = ModelHelper::select($schema['field'], $request, __CLASS__);
        ModelHelper::join($schema['join'], $request, $db);

        if ($params) {
            ModelHelper::dynamicFilterAnd($params, $request, $db, __CLASS__);
        }

        if ($or) {
            ModelHelper::dynamicFilterOr($or, $request, $db, __CLASS__);
        }

        $results = ModelHelper::generateAllResults($schema, $params, $request, $db, $append);

        return response()->json($results);
    }

    public static function createOrUpdate($params, $method, $request)
    {
        DB::beginTransaction();

        $filename = null;

        if (isset($params['_token']) && $params['_token']) {
            unset($params['_token']);
        }

        if (isset($params['id']) && $params['id']) {
            $old = self::getById($params['id'])->original;

            $update = self::where('id', $params['id'])->update($params);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Succesfully Updated Data',
                'data' => self::getById($params['id'])->original,
            ]);
        }

        $save = self::create($params);

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'Succesfully Added Data',
            'data' => self::getById($save->id)->original,
        ]);
    }

    public static function deleteById($id, $params, $request)
    {
        // $old = self::getById($id)->original;

        self::where('id', $id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Succesfully Deleted Data',
        ]);
    }

    public static function approveById($id, $params, $request)
    {
        // $data = self::getById($id)->original;

        return response()->json([
            'status' => 'success',
            'message' => 'Succesfully Approved Data',
            'data' => null,
        ]);
    }

    public static function getDataList(): array
    {
        $items_list = static::orderBy('id', 'desc')->get();

        return [
            'success' => true,
            'data' => $items_list,
        ];
    }

    public static function getSelect2Data(?string $search_term = null): array
    {
        $query_builder = static::query();

        if (! empty($search_term)) {
            $query_builder->where(function ($sub_query) use ($search_term) {
                $sub_query->where('name', 'like', '%'.$search_term.'%')
                    ->orWhere('code', 'like', '%'.$search_term.'%');
            });
        }

        $items_data = $query_builder->orderBy('name', 'asc')->limit(30)->get();

        $results_list = [];
        foreach ($items_data as $row) {
            $results_list[] = [
                'id' => $row->id,
                'text' => $row->code.' - '.$row->name.' (Stok: '.$row->stock.')',
                'price' => (float) $row->price,
                'stock' => (int) $row->stock,
            ];
        }

        return [
            'results' => $results_list,
        ];
    }

    public static function getDatatablesData(array $request_data): array
    {
        $totalData = static::count();

        $query_builder = static::query();

        $search_value = $request_data['search']['value'] ?? ($request_data['search'] ?? null);
        if (! empty($search_value)) {
            $query_builder->where(function ($sub_query) use ($search_value) {
                $sub_query->where('name', 'like', '%'.$search_value.'%')
                    ->orWhere('code', 'like', '%'.$search_value.'%');
            });
        }

        $totalFiltered = $query_builder->count();

        $start_offset = (int) ($request_data['start'] ?? 0);
        $page_length = (int) ($request_data['length'] ?? 10);

        if ($page_length > 0) {
            $query_builder->skip($start_offset)->take($page_length);
        }

        $items_collection = $query_builder->orderBy('id', 'desc')->get();

        $formatted_data = [];
        $row_number = $start_offset + 1;

        foreach ($items_collection as $item_row) {
            $stock_count = (int) $item_row->stock;
            if ($stock_count <= 0) {
                $stock_badge = '<span class="badge bg-danger">Habis</span>';
            } elseif ($stock_count <= 10) {
                $stock_badge = '<span class="badge bg-warning text-dark">Kritis ('.$stock_count.')</span>';
            } else {
                $stock_badge = '<span class="badge bg-success-subtle text-success border border-success-subtle">'.$stock_count.' unit</span>';
            }

            $action_html = '<div class="d-inline-flex gap-1">';
            $action_html .= '<button type="button" class="btn btn-sm btn-outline-warning btn-edit" data-id="'.$item_row->id.'" title="Edit"><i class="bi bi-pencil"></i></button>';
            $action_html .= '<button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="'.$item_row->id.'" data-name="'.htmlspecialchars($item_row->name, ENT_QUOTES).'" title="Hapus"><i class="bi bi-trash"></i></button>';
            $action_html .= '</div>';

            $formatted_data[] = [
                'no' => $row_number++,
                'id' => $item_row->id,
                'code' => $item_row->code,
                'name' => $item_row->name,
                'price' => (float) $item_row->price,
                'price_formatted' => 'Rp '.number_format((float) $item_row->price, 0, ',', '.'),
                'stock' => $stock_count,
                'stock_badge' => $stock_badge,
                'created_at' => $item_row->created_at ? $item_row->created_at->format('d/m/Y H:i') : '-',
                'action' => $action_html,
            ];
        }

        return [
            'draw' => (int) ($request_data['draw'] ?? 1),
            'recordsTotal' => (int) $totalData,
            'recordsFiltered' => (int) $totalFiltered,
            'data' => $formatted_data,
        ];
    }

    public static function getItemDetail(int $id): array
    {
        $item_record = static::find($id);

        if (! $item_record) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Data barang tidak ditemukan.',
                'data' => null,
            ];
        }

        return [
            'success' => true,
            'status' => 'success',
            'data' => $item_record,
        ];
    }

    public static function storeItem(array $data): array
    {
        $code = trim($data['code'] ?? '');
        $name = trim($data['name'] ?? '');
        $price = $data['price'] ?? null;
        $stock = $data['stock'] ?? null;

        if ($code === '' || $name === '' || $price === null || $stock === null) {
            return [
                'status' => 422,
                'body' => [
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Semua kolom barang wajib diisi.',
                ],
            ];
        }

        $existing_item = static::where('code', $code)->first();
        if ($existing_item) {
            return [
                'status' => 422,
                'body' => [
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Kode barang sudah digunakan.',
                ],
            ];
        }

        $new_item = static::create([
            'code' => $code,
            'name' => $name,
            'price' => (float) $price,
            'stock' => (int) $stock,
        ]);

        return [
            'status' => 200,
            'body' => [
                'success' => true,
                'status' => 'success',
                'message' => 'Data barang berhasil ditambahkan.',
                'data' => $new_item,
            ],
        ];
    }

    public static function updateItem(int $id, array $data): array
    {
        $item_record = static::find($id);

        if (! $item_record) {
            return [
                'status' => 404,
                'body' => [
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Data barang tidak ditemukan.',
                ],
            ];
        }

        $code = trim($data['code'] ?? $item_record->code);
        $name = trim($data['name'] ?? $item_record->name);
        $price = isset($data['price']) ? (float) $data['price'] : $item_record->price;
        $stock = isset($data['stock']) ? (int) $data['stock'] : $item_record->stock;

        $duplicate_code = static::where('code', $code)->where('id', '!=', $id)->first();
        if ($duplicate_code) {
            return [
                'status' => 422,
                'body' => [
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Kode barang sudah digunakan oleh item lain.',
                ],
            ];
        }

        $item_record->update([
            'code' => $code,
            'name' => $name,
            'price' => $price,
            'stock' => $stock,
        ]);

        return [
            'status' => 200,
            'body' => [
                'success' => true,
                'status' => 'success',
                'message' => 'Data barang berhasil diperbarui.',
                'data' => $item_record,
            ],
        ];
    }

    public static function deleteItem(int $id): array
    {
        $item_record = static::find($id);

        if (! $item_record) {
            return [
                'status' => 404,
                'body' => [
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Data barang tidak ditemukan.',
                ],
            ];
        }

        $item_record->forceDelete();

        return [
            'status' => 200,
            'body' => [
                'success' => true,
                'status' => 'success',
                'message' => 'Data barang berhasil dihapus.',
            ],
        ];
    }
}
