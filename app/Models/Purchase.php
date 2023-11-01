<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

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
        'note',
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
