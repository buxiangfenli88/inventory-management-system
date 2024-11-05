<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

/**
 * App\Models\Purchase
 *
 * @property int $id
 * @property string $supplier_id
 * @property string $purchase_date
 * @property string $purchase_no
 * @property string $purchase_status 0=Pending, 1=Approved
 * @property int $total_amount
 * @property string $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $note
 * @property-read \App\Models\Supplier|null $supplier
 * @property-read \App\Models\User|null $user_created
 * @property-read \App\Models\User|null $user_updated
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase query()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase roleFilter(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase sortable($defaultParameters = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase wherePurchaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase wherePurchaseNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase wherePurchaseStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Purchase extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'supplier_id',
        'purchase_date',
        'purchase_no',
        'purchase_status',
        'total_amount',
        'created_by',
        'updated_by',
    ];

    public $sortable = [
        'purchase_date',
        'total_amount',
    ];
    protected $guarded = [
        'id',
    ];

    protected $with = [
        'supplier',
        'user_created',
        'user_updated',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function user_created()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function user_updated()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('purchase_no', 'like', '%' . $search . '%');
                $query->orWhere('created_at', 'like', '%' . $search . '%');
                $query->orWhereHas('supplier', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                });
            });
        });
    }

    public function scopeRoleFilter($query, User $user)
    {
        if ($user->hasRole(UserRole::ADMIN)) {
            return;
        }

        $query->where('created_by', $user->id);
    }
}
