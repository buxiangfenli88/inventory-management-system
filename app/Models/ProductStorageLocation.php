<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\ProductStorageLocation
 *
 * @property int $id
 * @property int $product_id
 * @property int $storage_location_id
 * @property int $quantity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Product> $products
 * @property-read int|null $products_count
 * @property-read Collection<int, StorageLocation> $storageLocations
 * @property-read StorageLocation $storageLocation
 * @property-read int|null $storage_locations_count
 * @method static Builder|ProductStorageLocation newModelQuery()
 * @method static Builder|ProductStorageLocation newQuery()
 * @method static Builder|ProductStorageLocation onlyTrashed()
 * @method static Builder|ProductStorageLocation query()
 * @method static Builder|ProductStorageLocation sortable($defaultParameters = null)
 * @method static Builder|ProductStorageLocation whereCreatedAt($value)
 * @method static Builder|ProductStorageLocation whereId($value)
 * @method static Builder|ProductStorageLocation whereProductId($value)
 * @method static Builder|ProductStorageLocation whereQuantity($value)
 * @method static Builder|ProductStorageLocation whereStorageLocationId($value)
 * @method static Builder|ProductStorageLocation whereUpdatedAt($value)
 * @method static Builder|ProductStorageLocation withTrashed()
 * @method static Builder|ProductStorageLocation withoutTrashed()
 * @mixin \Eloquent
 */
class ProductStorageLocation extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'product_id',
        'storage_location_id',
        'quantity',
    ];

    protected $guarded = [
        'id',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_storage_location')
            ->withPivot('quantity');
    }

    public function storageLocations(): BelongsToMany
    {
        return $this->belongsToMany(StorageLocation::class, 'product_storage_location')
            ->withPivot('quantity');
    }

    public function storageLocation()
    {
        return $this->belongsTo(StorageLocation::class, 'storage_location_id');
    }
}
