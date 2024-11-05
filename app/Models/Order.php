<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property string $customer_id
 * @property string $order_date
 * @property string $order_status
 * @property int $total_products
 * @property int $sub_total
 * @property int $vat
 * @property int $total
 * @property string $invoice_no
 * @property string $payment_type
 * @property int $pay
 * @property int $due
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $created_by
 * @property string|null $note
 * @property-read \App\Models\Customer|null $customer
 * @property-read \App\Models\User|null $user_created
 * @method static Builder|Order filter(array $filters)
 * @method static Builder|Order newModelQuery()
 * @method static Builder|Order newQuery()
 * @method static Builder|Order query()
 * @method static Builder|Order roleFilter(\App\Models\User $user)
 * @method static Builder|Order sortable($defaultParameters = null)
 * @method static Builder|Order whereCreatedAt($value)
 * @method static Builder|Order whereCreatedBy($value)
 * @method static Builder|Order whereCustomerId($value)
 * @method static Builder|Order whereDue($value)
 * @method static Builder|Order whereId($value)
 * @method static Builder|Order whereInvoiceNo($value)
 * @method static Builder|Order whereNote($value)
 * @method static Builder|Order whereOrderDate($value)
 * @method static Builder|Order whereOrderStatus($value)
 * @method static Builder|Order wherePay($value)
 * @method static Builder|Order wherePaymentType($value)
 * @method static Builder|Order whereSubTotal($value)
 * @method static Builder|Order whereTotal($value)
 * @method static Builder|Order whereTotalProducts($value)
 * @method static Builder|Order whereUpdatedAt($value)
 * @method static Builder|Order whereVat($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'customer_id',
        'order_date',
        'order_status',
        'total_products',
        'sub_total',
        'vat',
        'total',
        'invoice_no',
        'payment_type',
        'pay',
        'due',
    ];

    public $sortable = [
        'customer_id',
        'order_date',
        'pay',
        'due',
        'total',
    ];

    protected $guarded = [
        'id',
    ];

    protected $with = [
        'customer',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function user_created()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('invoice_no', 'like', '%' . $search . '%');
                $query->orWhere('created_at', 'like', '%' . $search . '%');
                $query->orWhereHas('customer', function ($query) use ($search) {
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
