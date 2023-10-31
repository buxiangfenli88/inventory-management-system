<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

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
        'bien_so_xe',
        'category_id',
        'so_kien_giao',
        'note'
    ];

    protected $guarded = [
        'id',
    ];

    public $sortable = [
        'name',
        'email',
        'shopname',
        'bien_so_xe',
        'created_at'
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
