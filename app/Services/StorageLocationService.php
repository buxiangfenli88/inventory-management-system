<?php

namespace App\Services;

use App\Models\ProductStorageLocation;
use App\Models\StorageLocation;

class StorageLocationService
{
    public function exportStorageLocation(int $productId, int $storageLocationId, int $quantity): void
    {
        ProductStorageLocation::query()
            ->where('product_id', $productId)
            ->where('storage_location_id', $storageLocationId)
            ->decrement('quantity', $quantity);

        StorageLocation::query()
            ->where('id', $storageLocationId)
            ->increment('stock_remain', $quantity);
    }

    public function inputStorageLocation(int $productId, int $storageLocationId, int $quantity): void
    {
        // update or create product location quantity
        ProductStorageLocation::query()->firstOrCreate(
            [
                'storage_location_id' => $storageLocationId,
                'product_id' => $productId,
            ],
            [
                'quantity' => 0,
            ]
        );

        ProductStorageLocation::query()
            ->where('product_id', $productId)
            ->where('storage_location_id', $storageLocationId)
            ->increment('quantity', $quantity);

        StorageLocation::query()
            ->where('id', $storageLocationId)
            ->decrement('stock_remain', $quantity);
    }
}
