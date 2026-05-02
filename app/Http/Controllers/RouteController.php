<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Base\BaseControllerAdmin;

use App\Models\Admin;
use App\Models\AssignModule;
use App\Models\Module;
use App\Models\Category;
use App\Models\Franchise;
use App\Models\Product;
use App\Models\Pool;
use App\Models\CustomerService;
use App\Models\Stock;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\DeliveryTimeSchedule;
use App\Models\Setting;
use App\Models\OrderStatus;
use App\Models\PromoCode;
use App\Models\Customer;
use App\Models\Message;
use App\Models\MessageUser;
use App\Models\AssignProduct;
use App\Models\CmsPage;
use App\Models\Banner;
use App\Models\StockOrder;
use App\Models\StockOrderDetail;
use App\Models\CustomerServiceHour;
use App\Models\StockProduct;
use App\Models\Allergen;
use App\Models\AssignAllergen;
use App\Models\WareHouse;
use App\Models\FranchiseStockOrder;
use App\Models\PaymentMethod;
use App\Models\Channel;
use App\Models\ProductType;
use App\Models\UberStore;
use App\Models\InvoicePdf;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * @deprecated TODO!: Remove this later
 */
class RouteController extends BaseControllerAdmin
{
}
