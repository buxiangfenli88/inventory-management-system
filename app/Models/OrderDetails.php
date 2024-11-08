<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\OrderDetails
 *
 * @property int $id
 * @property string $order_id
 * @property string $product_id
 * @property int $quantity
 * @property int $unitcost
 * @property int $total
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Order|null $order
 * @property-read Product|null $product
 * @method static Builder|OrderDetails newModelQuery()
 * @method static Builder|OrderDetails newQuery()
 * @method static Builder|OrderDetails query()
 * @method static Builder|OrderDetails whereCreatedAt($value)
 * @method static Builder|OrderDetails whereId($value)
 * @method static Builder|OrderDetails whereOrderId($value)
 * @method static Builder|OrderDetails whereProductId($value)
 * @method static Builder|OrderDetails whereQuantity($value)
 * @method static Builder|OrderDetails whereTotal($value)
 * @method static Builder|OrderDetails whereUnitcost($value)
 * @method static Builder|OrderDetails whereUpdatedAt($value)
 * @property int|null $storage_location_id
 * @property string|null $storage_location_name
 * @method static Builder|OrderDetails whereStorageLocationId($value)
 * @method static Builder|OrderDetails whereStorageLocationName($value)
 * @mixin \Eloquent
 */
class OrderDetails extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
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

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
