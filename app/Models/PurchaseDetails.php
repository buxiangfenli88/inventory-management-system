<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\PurchaseDetails
 *
 * @property int $id
 * @property string $purchase_id
 * @property string $product_id
 * @property int $quantity
 * @property int $unitcost
 * @property int $total
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Product|null $product
 * @property-read Purchase|null $purchase
 * @property-read StorageLocation $storageLocation
 * @method static Builder|PurchaseDetails newModelQuery()
 * @method static Builder|PurchaseDetails newQuery()
 * @method static Builder|PurchaseDetails query()
 * @method static Builder|PurchaseDetails whereCreatedAt($value)
 * @method static Builder|PurchaseDetails whereId($value)
 * @method static Builder|PurchaseDetails whereProductId($value)
 * @method static Builder|PurchaseDetails wherePurchaseId($value)
 * @method static Builder|PurchaseDetails whereQuantity($value)
 * @method static Builder|PurchaseDetails whereTotal($value)
 * @method static Builder|PurchaseDetails whereUnitcost($value)
 * @method static Builder|PurchaseDetails whereUpdatedAt($value)
 * @property int|null $storage_location_id
 * @method static Builder|PurchaseDetails whereStorageLocationId($value)
 * @mixin \Eloquent
 */
class PurchaseDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'unitcost',
        'total',
    ];

    protected $guarded = [
        'id',
    ];

    protected $with = ['product'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'id');
    }

    public function storageLocation(): BelongsTo
    {
        return $this->belongsTo(StorageLocation::class, 'storage_location_id', 'id');
    }
}
