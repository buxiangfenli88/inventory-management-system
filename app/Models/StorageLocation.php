<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\StorageLocation
 *
 * @property integer $id
 * @property string $name
 * @property integer $stock
 * @property integer $stock_remain
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @method static Builder|StorageLocation filter(array $filters)
 * @method static Builder|StorageLocation newModelQuery()
 * @method static Builder|StorageLocation newQuery()
 * @method static Builder|StorageLocation onlyTrashed()
 * @method static Builder|StorageLocation query()
 * @method static Builder|StorageLocation sortable($defaultParameters = null)
 * @method static Builder|StorageLocation whereCreatedAt($value)
 * @method static Builder|StorageLocation whereDeletedAt($value)
 * @method static Builder|StorageLocation whereId($value)
 * @method static Builder|StorageLocation whereName($value)
 * @method static Builder|StorageLocation whereStock($value)
 * @method static Builder|StorageLocation whereUpdatedAt($value)
 * @method static Builder|StorageLocation withTrashed()
 * @method static Builder|StorageLocation withoutTrashed()
 * @mixin \Eloquent
 */
class StorageLocation extends Model
{
    use HasFactory, Sortable, SoftDeletes;

    protected $fillable = [
        'name',
        'stock',
    ];

    protected $sortable = [
        'name',
        'stock',
    ];

    protected $guarded = [
        'id',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%');
        });
    }

    public function getRouteKeyName()
    {
        return 'id';
    }

    public function updateStockRemain()
    {
        $this->stock_remain = $this->stock - Product::query()->where('storage_location_id', $this->id)->sum('stock');
        $this->save();
    }

    public function getStockRemain(?int $ignoreProductId = null)
    {
        return $this->stock - Product::query()
            ->where('storage_location_id', $this->id)
            ->where('id', '!=', $ignoreProductId)
            ->sum('stock');
    }
}
