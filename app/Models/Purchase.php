<?php

namespace App\Models;

use App\Enums\UserRole;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $note
 * @property-read Supplier|null $supplier
 * @property-read User|null $user_created
 * @property-read User|null $user_updated
 * @property-read Collection<PurchaseDetails> $purchaseDetails
 * @method static Builder|Purchase filter(array $filters)
 * @method static Builder|Purchase newModelQuery()
 * @method static Builder|Purchase newQuery()
 * @method static Builder|Purchase query()
 * @method static Builder|Purchase roleFilter(User $user)
 * @method static Builder|Purchase sortable($defaultParameters = null)
 * @method static Builder|Purchase whereCreatedAt($value)
 * @method static Builder|Purchase whereCreatedBy($value)
 * @method static Builder|Purchase whereId($value)
 * @method static Builder|Purchase whereNote($value)
 * @method static Builder|Purchase wherePurchaseDate($value)
 * @method static Builder|Purchase wherePurchaseNo($value)
 * @method static Builder|Purchase wherePurchaseStatus($value)
 * @method static Builder|Purchase whereSupplierId($value)
 * @method static Builder|Purchase whereTotalAmount($value)
 * @method static Builder|Purchase whereUpdatedAt($value)
 * @method static Builder|Purchase whereUpdatedBy($value)
 * @mixin Eloquent
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

    public function purchaseDetails(): HasMany
    {
        return $this->hasMany(PurchaseDetails::class, 'purchase_id', 'id');
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
