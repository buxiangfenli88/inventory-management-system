<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderDetails
 *
 * @property int $id
 * @property string $order_id
 * @property string $product_id
 * @property int $quantity
 * @property int $unitcost
 * @property int $total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails whereUnitcost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails whereUpdatedAt($value)
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
