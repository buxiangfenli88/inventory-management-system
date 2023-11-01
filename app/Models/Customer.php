<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory, Sortable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'photo',
        'account_holder',
        'account_number',
        'bank_name',
        'bien_so_xe',
        'so_kien_giao',
        'note'
    ];

    protected $guarded = [
        'id',
    ];

    public $sortable = [
        'name',
        'email',
        'created_at',
        'bien_so_xe',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%')
                ->orWhere('bien_so_xe', 'like', '%' . $search . '%')
                ;
        });
    }
}
