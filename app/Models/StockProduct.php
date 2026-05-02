<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Data retrieved from SyncProductFromStockController->syncProductFromStock
 *
 * Name of the model should be ProductFromStockApi, or ProductFromStock
 */
class StockProduct extends Model
{
    use SoftDeletes;
}
