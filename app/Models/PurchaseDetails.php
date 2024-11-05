<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PurchaseDetails
 *
 * @property int $id
 * @property string $purchase_id
 * @property string $product_id
 * @property int $quantity
 * @property int $unitcost
 * @property int $total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\Purchase|null $purchase
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetails whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetails whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetails whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetails wherePurchaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetails whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetails whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetails whereUnitcost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetails whereUpdatedAt($value)
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
}
