<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

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
        'note',
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
        return $this->belongsTo(Customer::class, 'customer_id', 'id')->withTrashed();
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
