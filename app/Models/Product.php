<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Product
 *
 * @property int $id
 * @property string $product_name
 * @property string $category_id
 * @property string|null $unit_id
 * @property string|null $product_code
 * @property int $stock
 * @property int|null $buying_price
 * @property int|null $selling_price
 * @property string|null $product_image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $storage_location_id
 * @property-read \App\Models\Category|null $category
 * @property-read \App\Models\Unit|null $unit
 * @method static \Database\Factories\ProductFactory factory($count = null, $state = [])
 * @method static Builder|Product filter(array $filters)
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product query()
 * @method static Builder|Product sortable($defaultParameters = null)
 * @method static Builder|Product whereBuyingPrice($value)
 * @method static Builder|Product whereCategoryId($value)
 * @method static Builder|Product whereCreatedAt($value)
 * @method static Builder|Product whereId($value)
 * @method static Builder|Product whereProductCode($value)
 * @method static Builder|Product whereProductImage($value)
 * @method static Builder|Product whereProductName($value)
 * @method static Builder|Product whereSellingPrice($value)
 * @method static Builder|Product whereStock($value)
 * @method static Builder|Product whereStorageLocationId($value)
 * @method static Builder|Product whereUnitId($value)
 * @method static Builder|Product whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StorageLocation> $storageLocations
 * @property-read int|null $storage_locations_count
 * @mixin \Eloquent
 */
class Product extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'product_name',
        'category_id',
        'unit_id',
        'product_code',
        'stock',
        'buying_price',
        'selling_price',
        'product_image',
        'storage_location_id',
    ];

    public $sortable = [
        'product_name',
        'category_id',
        'unit_id',
        'product_code',
        'stock',
        'buying_price',
        'selling_price',
    ];

    protected $guarded = [
        'id',
    ];

    protected $with = [
        'category',
        'unit',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function storageLocations()
    {
        return $this->hasMany(StorageLocation::class, 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where(function($query)use($search){
                $query->where('product_name', 'like', '%' . $search . '%');
                $query->orWhere('product_code', 'like', '%' . $search . '%');
            });
        });
    }
}
