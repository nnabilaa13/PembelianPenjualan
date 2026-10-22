<?php

namespace App\Models;

use App\Helpers\ModelHelper;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string number
 * @property Date   date
 * @property int    user_id
 * @property int    created_at
 * @property int    updated_at
 * @property int    deleted_at
 */
class Purchases extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'purchases';

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
        'number',
        'date',
        'user_id',
        'created_at',
        'updated_at',
        'deleted_at',
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
        'number' => 'string', 'date' => 'date', 'user_id' => 'int', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'datetime',
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
    public function details(): HasMany
    {
        return $this->hasMany(PurchaseDetails::class, 'purchase_id');
    }

    public static function mapSchema($params = [], $user = [])
    {
        $model = new self;

        return [
            'field' => [
                'id' => ['column' => $model->table.'.id', 'alias' => 'id', 'type' => 'int'],
                'number' => ['column' => $model->table.'.number', 'alias' => 'number', 'type' => 'string'],
                'date' => ['column' => $model->table.'.date', 'alias' => 'date', 'type' => 'date'],
                'user_id' => ['column' => $model->table.'.user_id', 'alias' => 'user_id', 'type' => 'int'],
                'created_at' => ['column' => $model->table.'.created_at', 'alias' => 'created_at', 'type' => 'date'],
                'updated_at' => ['column' => $model->table.'.updated_at', 'alias' => 'updated_at', 'type' => 'date'],
                'deleted_at' => ['column' => $model->table.'.deleted_at', 'alias' => 'deleted_at', 'type' => 'date'],
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

    public static function storePurchase(array $data, ?object $session_user): array
    {
        $user_role = $session_user->role ?? null;
        if (! in_array($user_role, ['SuperAdmin', 'Purchase'], true)) {
            return [
                'status' => 403,
                'body' => [
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Akses ditolak: Anda tidak memiliki izin untuk tindakan ini.',
                ],
            ];
        }

        $purchase_number = trim($data['number'] ?? '');
        $purchase_date = trim($data['date'] ?? '');
        $items_list = $data['items'] ?? [];

        if ($purchase_number === '' || $purchase_date === '' || empty($items_list) || ! is_array($items_list)) {
            return [
                'status' => 422,
                'body' => [
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Nomor pembelian, tanggal, dan minimal satu barang wajib diisi.',
                ],
            ];
        }

        $user_id = $session_user->id ?? null;

        $new_purchase = static::create([
            'number' => $purchase_number,
            'date' => $purchase_date,
            'user_id' => $user_id,
        ]);

        foreach ($items_list as $item_data) {
            $inventory_id = (int) ($item_data['inventory_id'] ?? 0);
            $qty = (int) ($item_data['qty'] ?? 0);
            $price = (float) ($item_data['price'] ?? 0);

            if ($inventory_id > 0 && $qty > 0) {
                PurchaseDetails::create([
                    'purchase_id' => $new_purchase->id,
                    'inventory_id' => $inventory_id,
                    'qty' => $qty,
                    'price' => $price,
                ]);

                Inventories::where('id', $inventory_id)->increment('stock', $qty);
            }
        }

        return [
            'status' => 200,
            'body' => [
                'success' => true,
                'status' => 'success',
                'message' => 'Transaksi pembelian berhasil disimpan dan stok telah diperbarui.',
                'data' => $new_purchase,
            ],
        ];
    }

    public static function updatePurchase(int $id, array $data, ?object $session_user): array
    {
        $user_role = $session_user->role ?? null;
        if (! in_array($user_role, ['SuperAdmin', 'Purchase'], true)) {
            return [
                'status' => 403,
                'body' => [
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Akses ditolak: Anda tidak memiliki izin untuk tindakan ini.',
                ],
            ];
        }

        $purchase = static::find($id);
        if (! $purchase) {
            return [
                'status' => 404,
                'body' => [
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Data pembelian tidak ditemukan.',
                ],
            ];
        }

        $purchase_number = trim($data['number'] ?? '');
        $purchase_date = trim($data['date'] ?? '');
        $items_list = $data['items'] ?? [];

        if ($purchase_number === '' || $purchase_date === '' || empty($items_list) || ! is_array($items_list)) {
            return [
                'status' => 422,
                'body' => [
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Nomor pembelian, tanggal, dan minimal satu barang wajib diisi.',
                ],
            ];
        }

        DB::beginTransaction();
        try {
            // Kembalikan stok lama sebelum dihapus
            $old_details = PurchaseDetails::where('purchase_id', $purchase->id)->get();
            foreach ($old_details as $old_detail) {
                Inventories::where('id', $old_detail->inventory_id)->decrement('stock', (int) $old_detail->qty);
            }

            PurchaseDetails::where('purchase_id', $purchase->id)->delete();

            $purchase->update([
                'number' => $purchase_number,
                'date' => $purchase_date,
            ]);

            foreach ($items_list as $item_data) {
                $inventory_id = (int) ($item_data['inventory_id'] ?? 0);
                $qty = (int) ($item_data['qty'] ?? 0);
                $price = (float) ($item_data['price'] ?? 0);

                if ($inventory_id > 0 && $qty > 0) {
                    PurchaseDetails::create([
                        'purchase_id' => $purchase->id,
                        'inventory_id' => $inventory_id,
                        'qty' => $qty,
                        'price' => $price,
                    ]);

                    Inventories::where('id', $inventory_id)->increment('stock', $qty);
                }
            }

            DB::commit();

            return [
                'status' => 200,
                'body' => [
                    'success' => true,
                    'status' => 'success',
                    'message' => 'Transaksi pembelian berhasil diperbarui dan stok telah disesuaikan.',
                    'data' => $purchase->fresh(),
                ],
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'status' => 500,
                'body' => [
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan saat memperbarui transaksi pembelian: '.$e->getMessage(),
                ],
            ];
        }
    }

    public static function getDatatablesData(array $request_data, ?object $session_user): array
    {
        $query_builder = static::query();

        $totalData = $query_builder->count();

        $search_value = $request_data['search']['value'] ?? ($request_data['search'] ?? null);
        if (! empty($search_value)) {
            $query_builder->where('number', 'like', '%'.$search_value.'%');
        }

        $totalFiltered = $query_builder->count();

        $start_offset = (int) ($request_data['start'] ?? 0);
        $page_length = (int) ($request_data['length'] ?? 10);

        if ($page_length > 0) {
            $query_builder->skip($start_offset)->take($page_length);
        }

        $purchases_collection = $query_builder->orderBy('id', 'desc')->get();

        $formatted_data = [];
        $row_number = $start_offset + 1;

        foreach ($purchases_collection as $purchase_item) {
            $creator_name = Users::where('id', $purchase_item->user_id)->value('name') ?? '-';

            $details_list = PurchaseDetails::where('purchase_id', $purchase_item->id)->get();
            $total_items = (int) $details_list->sum('qty');
            $total_amount = (float) $details_list->sum(function ($detail_row) {
                return (int) $detail_row->qty * (float) $detail_row->price;
            });

            $print_url = route('purchases.print', $purchase_item->id);

            $action_html = '<div class="d-inline-flex gap-1">';
            $action_html .= '<button type="button" class="btn btn-sm btn-outline-info btn-detail" data-id="'.$purchase_item->id.'" title="Lihat Rincian"><i class="bi bi-eye"></i></button>';
            if ($session_user && in_array($session_user->role, ['SuperAdmin', 'Purchase'], true)) {
                $action_html .= '<button type="button" class="btn btn-sm btn-outline-warning btn-edit" data-id="'.$purchase_item->id.'" title="Edit"><i class="bi bi-pencil"></i></button>';
            }
            $action_html .= '<a href="'.$print_url.'" target="_blank" class="btn btn-sm btn-outline-secondary" title="Cetak Faktur"><i class="bi bi-printer"></i></a>';
            if ($session_user && in_array($session_user->role, ['SuperAdmin', 'Purchase'], true)) {
                $action_html .= '<button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="'.$purchase_item->id.'" data-number="'.htmlspecialchars($purchase_item->number, ENT_QUOTES).'" title="Hapus"><i class="bi bi-trash"></i></button>';
            }
            $action_html .= '</div>';

            $formatted_data[] = [
                'no' => $row_number++,
                'id' => $purchase_item->id,
                'number' => $purchase_item->number,
                'date' => $purchase_item->date ? date('d/m/Y', strtotime($purchase_item->date)) : '-',
                'creator_name' => $creator_name,
                'total_items' => $total_items,
                'total_amount_formatted' => 'Rp '.number_format($total_amount, 0, ',', '.'),
                'created_at' => $purchase_item->created_at ? $purchase_item->created_at->format('d/m/Y H:i') : '-',
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

    public static function getPurchaseDetail(int $id): array
    {
        $purchase_item = static::find($id);

        if (! $purchase_item) {
            return [
                'status' => 404,
                'body' => [
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Data pembelian tidak ditemukan.',
                ],
            ];
        }

        $detail_array = $purchase_item->toDetailArray();

        return [
            'status' => 200,
            'body' => [
                'success' => true,
                'status' => 'success',
                'data' => $detail_array,
            ],
        ];
    }

    public function toDetailArray(): array
    {
        $creator_name = Users::where('id', $this->user_id)->value('name') ?? '-';

        $details_records = PurchaseDetails::where('purchase_id', $this->id)->get();

        $items_data = [];
        $grand_total = 0;

        foreach ($details_records as $detail_row) {
            $inventory_item = Inventories::where('id', $detail_row->inventory_id)->first();
            $subtotal = (int) $detail_row->qty * (float) $detail_row->price;
            $grand_total += $subtotal;

            $items_data[] = [
                'id' => $detail_row->id,
                'inventory_id' => $detail_row->inventory_id,
                'inventory_code' => $inventory_item ? $inventory_item->code : '-',
                'inventory_name' => $inventory_item ? $inventory_item->name : 'Barang Dihapus',
                'qty' => (int) $detail_row->qty,
                'price' => (float) $detail_row->price,
                'stock' => $inventory_item ? (int) $inventory_item->stock : 0,
                'subtotal' => $subtotal,
                'price_formatted' => 'Rp '.number_format((float) $detail_row->price, 0, ',', '.'),
                'subtotal_formatted' => 'Rp '.number_format($subtotal, 0, ',', '.'),
            ];
        }

        return [
            'id' => $this->id,
            'number' => $this->number,
            'date' => $this->date ? date('Y-m-d', strtotime($this->date)) : null,
            'creator_name' => $creator_name,
            'grand_total' => $grand_total,
            'grand_total_formatted' => 'Rp '.number_format($grand_total, 0, ',', '.'),
            'items' => $items_data,
        ];
    }

    public static function deletePurchase(int $id, ?object $session_user = null): array
    {
        $user_role = $session_user->role ?? null;

        if ($user_role && ! in_array($user_role, ['SuperAdmin', 'Purchase'], true)) {
            return [
                'status' => 403,
                'body' => [
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Akses ditolak: Anda tidak memiliki izin untuk tindakan ini.',
                ],
            ];
        }

        $purchase_item = static::find($id);

        if (! $purchase_item) {
            return [
                'status' => 404,
                'body' => [
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Data pembelian tidak ditemukan.',
                ],
            ];
        }

        // Kembalikan stok ke inventori sebelum hapus
        $details = PurchaseDetails::where('purchase_id', $id)->get();
        foreach ($details as $detail) {
            Inventories::where('id', $detail->inventory_id)->decrement('stock', (int) $detail->qty);
        }

        PurchaseDetails::where('purchase_id', $id)->delete();
        $purchase_item->delete();

        return [
            'status' => 200,
            'body' => [
                'success' => true,
                'status' => 'success',
                'message' => 'Data pembelian berhasil dihapus dan stok telah disesuaikan.',
            ],
        ];
    }
}
