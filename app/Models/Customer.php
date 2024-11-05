<?php

namespace App\Models;

use Database\Factories\CustomerFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Customer
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $photo
 * @property string|null $account_holder
 * @property string|null $account_number
 * @property string|null $bank_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $bien_so_xe
 * @property string|null $so_kien_giao
 * @property string|null $note
 * @property int|null $created_by
 * @method static CustomerFactory factory($count = null, $state = [])
 * @method static Builder|Customer filter(array $filters)
 * @method static Builder|Customer newModelQuery()
 * @method static Builder|Customer newQuery()
 * @method static Builder|Customer query()
 * @method static Builder|Customer sortable($defaultParameters = null)
 * @method static Builder|Customer whereAccountHolder($value)
 * @method static Builder|Customer whereAccountNumber($value)
 * @method static Builder|Customer whereAddress($value)
 * @method static Builder|Customer whereBankName($value)
 * @method static Builder|Customer whereBienSoXe($value)
 * @method static Builder|Customer whereCreatedAt($value)
 * @method static Builder|Customer whereCreatedBy($value)
 * @method static Builder|Customer whereDeletedAt($value)
 * @method static Builder|Customer whereEmail($value)
 * @method static Builder|Customer whereId($value)
 * @method static Builder|Customer whereName($value)
 * @method static Builder|Customer whereNote($value)
 * @method static Builder|Customer wherePhone($value)
 * @method static Builder|Customer wherePhoto($value)
 * @method static Builder|Customer whereSoKienGiao($value)
 * @method static Builder|Customer whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Customer extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
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
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%')->orWhere('email', 'like', '%' . $search . '%');
        });
    }
}
