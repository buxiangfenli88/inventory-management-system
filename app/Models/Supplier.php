<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

/**
 * App\Models\Supplier
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $shopname
 * @property string|null $type
 * @property string|null $photo
 * @property string|null $account_holder
 * @property string|null $account_number
 * @property string|null $bank_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $bien_so_xe
 * @property int|null $category_id
 * @property string|null $so_kien_giao
 * @property string|null $note
 * @property string|null $deleted_at
 * @property int|null $created_by
 * @method static \Database\Factories\SupplierFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier query()
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier sortable($defaultParameters = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereAccountHolder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereBienSoXe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereShopname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereSoKienGiao($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Supplier extends Model
{
    use HasFactory, Sortable;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'shopname',
        'type',
        'photo',
        'account_holder',
        'account_number',
        'bank_name',
    ];

    protected $guarded = [
        'id',
    ];

    public $sortable = [
        'name',
        'email',
        'shopname',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%')->orWhere('shopname', 'like', '%' . $search . '%');
        });
    }
}
