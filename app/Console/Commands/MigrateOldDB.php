<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\Allergen;
use App\Models\Banner;
use App\Models\Cart;
use App\Models\Channel;
use App\Models\CmsPage;
use App\Models\ContactUs;
use App\Models\CustomerAddress;
use App\Models\CustomerService;
use App\Models\CustomerServiceHour;
use App\Models\DeliveryHistory;
use App\Models\DeliveryImage;
use App\Models\DeliveryPerson;
use App\Models\DeliveryTimeSchedule;
use App\Models\Favourite;
use App\Models\Franchise;
use App\Models\FranchiseStockOrder;
use App\Models\Help;
use App\Models\InvoicePdf;
use App\Models\Message;
use App\Models\MessageUser;
use App\Models\Module;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\OrderStatus;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\PromoCode;
use App\Models\RateAndReview;
use App\Models\Schedule;
use App\Models\ScheduleAbsense;
use App\Models\StockOrder;
use App\Models\StockOrderDetail;
use App\Models\StockProduct;
use App\Models\SubDeliveryPerson;
use App\Models\TakeawayTemp;
use App\Models\Uber;
use App\Models\UsedPromoCode;
use App\Models\Warehouse;
use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use ReflectionClass;

class MigrateOldDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-db {database} {--driver=} {--host=} {--username=} {--password=} {--charset=} {--collation=} {--prefix=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports data from an old application with a different table/column/schema';

    /**
     * @var ConnectionInterface|Connection
     */
    protected $oldConnection;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $default = DB::getDefaultConnection();
        config(['database.connections.old_db' => [
            'driver'    => $this->option('driver') ?: config("database.connections.$default.driver"),
            'host'      => $this->option('host') ?: config("database.connections.$default.host"),
            'database'  => $this->argument('database') ?: config("database.connections.$default.database"),
            'username'  => $this->option('username') ?: config("database.connections.$default.username"),
            'password'  => $this->option('password') ?: config("database.connections.$default.password"),
            'charset'   => $this->option('charset') ?: config("database.connections.$default.charset"),
            'collation' => $this->option('collation') ?: config("database.connections.$default.collation"),
            'prefix'    => $this->option('prefix') ?: config("database.connections.$default.prefix"),
        ]]);

        $this->oldConnection = DB::connection('old_db');

        $models = array_filter(glob(app_path('Models/*.php')), 'is_file');

        foreach ($models as $model) {
            $modelClass = 'App\Models\\'.basename($model, '.php');

            if (class_exists($modelClass)) {
                $tableName = (new $modelClass)->getTable();
                if (! Schema::hasTable($tableName)) {
                    $this->warn("Table $tableName not found");

                    continue;
                }

                if ($modelClass::count()) {
                    $this->error('Database already have records. Migrate old db canceled');

                    return;
                }
            }
        }

        DB::beginTransaction();

        $this->executeMigrations();
        // $this->migrateCustomersTable();
        // $this->migrateCustomersadressTable();

        if (! app()->isProduction()) {
            $this->setCredentialFranchiseForLogin();
            $this->setCredentialCustomerServiceForLogin();
        }

        DB::commit();
    }

    /**
     * Execute migrations
     *
     * Ignored table: cart, country, table 46, table 47, temp
     */
    public function executeMigrations()
    {
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if (str($method)->startsWith('migrate') && $method !== 'migrateOldTableToNewTable') {
                $this->invokeProtectedMethod($method);
            }
        }
    }

    private function invokeProtectedMethod($methodName)
    {
        $reflection = new ReflectionClass($this);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        $method->invoke($this);
    }

    /**
     * @param  array<string,string>  $columns  with format:
     *                                         ['old_column_1' => 'column_1', 'old_column_2' => 'column_2', ...]
     * @param  null|array<string, callable(object $oldRow)>  $resolveColumns  resolve the old columns when inserted to new table.
     * @param  null|string  $newTableName  If null, use the $oldTableName.
     * @param  null|array  $oldIds  filter old table where in old id(s)
     * @param  null|string  $oldPrimaryKey  If null, use first index's key $columns parameter.
     */
    protected function migrateOldTableToNewTable(string $oldTableName, array $columns, ?array $resolveColumns = null, ?string $newTableName = null, ?array $oldIds = null, ?string $oldPrimaryKey = null): void
    {
        $newTableName = $newTableName ?? $oldTableName;

        $oldPrimaryKey = $oldPrimaryKey ?? array_keys($columns)[0];

        $tableMigrationName = $oldTableName != $newTableName ? "{$oldTableName} -> {$newTableName}" : $oldTableName;

        $this->components->task("Migrate {$tableMigrationName} table.", function () use ($oldTableName, $newTableName, $columns, $resolveColumns, $oldIds, $oldPrimaryKey) {
            $oldTableQuery = $this->oldConnection->table($oldTableName)
                ->select(array_keys($columns))
                ->when(is_array($oldIds), function (Builder $query) use ($oldIds, $oldPrimaryKey) {
                    $query->whereIn($oldPrimaryKey, $oldIds);
                });

            if (is_array($resolveColumns)) {
                $oldTableQuery->lazyById(1000, $oldPrimaryKey)->each(function (object $oldRow) use ($newTableName, $columns, $resolveColumns) {
                    $insertedColumns = [];
                    foreach ($columns as $oldColumn => $newColumn) {
                        $insertedColumns[$newColumn] = array_key_exists($oldColumn, $resolveColumns) ? $resolveColumns[$oldColumn]($oldRow) : $oldRow->{$oldColumn};
                    }

                    try {
                        DB::table($newTableName)->insert($insertedColumns);
                    } catch (\Throwable $th) {
                        // For debugging
                        dump(json_encode($insertedColumns));
                        throw $th;
                    }
                });

                return;
            }

            DB::table($newTableName)->insertUsing(
                array_values($columns),
                $oldTableQuery
            );
        });

        if (is_array($oldIds)) {
            $skipped = $this->oldConnection->table($oldTableName)->whereNotIn($oldPrimaryKey, $oldIds)->pluck($oldPrimaryKey);
            if ($skipped->isNotEmpty()) {
                $this->warn('SKIPPED '.$oldTableName.' rows '.$oldPrimaryKey.' ('.implode(',', $skipped->all()).')');
            }
        }
    }

    protected function migrateAdminsTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'admin',
            newTableName: (new Admin)->getTable(),
            columns: [
                'admin_id'              => 'id',
                'name'                  => 'name',
                'email'                 => 'email',
                'password'              => 'password',
                'image'                 => 'image',
                'reset_token'           => 'reset_token',
                'admin_type'            => 'admin_type',
                'is_accountant'         => 'is_accountant',
                'admin_mobileno'        => 'admin_mobile_no',
                'admin_phone'           => 'admin_phone',
                'admin_street'          => 'admin_street',
                'admin_city'            => 'admin_city',
                'admin_state'           => 'admin_state',
                'admin_postcode'        => 'admin_postcode',
                'admin_company'         => 'admin_company',
                'admin_vat'             => 'admin_vat',
                'admin_commerce_number' => 'admin_commerce_number',
                'created_at'            => 'created_at',
                'updated_at'            => 'updated_at',
                'deleted_at'            => 'deleted_at',
            ]
        );
    }

    protected function migrateModulesTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'module',
            newTableName: (new Module)->getTable(),
            columns: [
                'id'          => 'id',
                'module_name' => 'module_name',
                'created_at'  => 'created_at',
                'updated_at'  => 'updated_at',
                'deleted_at'  => 'deleted_at',
            ]
        );
    }

    /**
     * Skip where admin_id doesn't exists
     */
    protected function migrateAdminModuleTable(): void
    {
        $adminIds = Admin::whereIn('id', $this->oldConnection->table('assign_module')->pluck('admin_id'))->pluck('id');
        $oldIds = $this->oldConnection->table('assign_module')->whereIn('admin_id', $adminIds)->pluck('assign_id');
        $this->migrateOldTableToNewTable(
            oldTableName: 'assign_module',
            newTableName: 'admin_module',
            columns: [
                'admin_id'   => 'admin_id',
                'module_id'  => 'module_id',
                'created_at' => 'created_at',
            ],
            oldIds: $oldIds->all(),
            oldPrimaryKey: 'assign_id',
        );
    }

    protected function migrateProductTypesTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'product_type',
            newTableName: (new ProductType)->getTable(),
            columns: [
                'product_type_id' => 'id',
                'product_type'    => 'product_type',
                'created_at'      => 'created_at',
                'updated_at'      => 'updated_at',
                'deleted_at'      => 'deleted_at',
            ]
        );
    }

    protected function migrateCategoriesTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'categories',
            columns: [
                'category_id'       => 'id',
                'category_parentid' => 'category_id',
                'category_name'     => 'category_name',
                'image'             => 'image',
                'description'       => 'description',
                'is_popular'        => 'is_popular',
                'category_order'    => 'category_order',
                'uber_product_type' => 'product_type_id',
                'created_at'        => 'created_at',
                'updated_at'        => 'updated_at',
                'deleted_at'        => 'deleted_at',
            ],
            resolveColumns: [
                'category_parentid' => fn ($oldRow) => $oldRow->category_parentid == 0 ? null : $oldRow->category_parentid,
                'uber_product_type' => fn ($oldRow) => in_array($oldRow->uber_product_type, [0, 3]) ? null : $oldRow->uber_product_type,
            ]
        );
    }

    protected function migratePoolsTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'pools',
            columns: [
                'pool_id'            => 'id',
                'from_postcode'      => 'from_postcode',
                'to_postcode'        => 'to_postcode',
                'area'               => 'area',
                'delivery_charge'    => 'delivery_charge',
                'delivery_startfrom' => 'delivery_start_from',
                'delivery_freefrom'  => 'delivery_free_from',
                'created_at'         => 'created_at',
                'updated_at'         => 'updated_at',
            ],
            resolveColumns: [
                'deleted_at' => fn ($oldRow) => $oldRow->deleted_at ? now()->createFromFormat('Y-m-d H:i:s', $oldRow->updated_at) : null,
            ],
        );
    }

    protected function migrateFranchisesTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'franchises',
            columns: [
                'franchise_id'        => 'id',
                'franchises_name'     => 'franchises_name',
                'franchises_no'       => 'franchises_no',
                'image'               => 'image',
                'franchises_email'    => 'franchises_email',
                'password'            => 'password',
                'reset_token'         => 'reset_token',
                'franchises_username' => 'franchises_username',
                'first_name'          => 'first_name',
                'last_name'           => 'last_name',
                'mobile_no'           => 'mobile_no',
                'date_of_birth'       => 'date_of_birth',
                'company_name'        => 'company_name',
                'house_no_street'     => 'house_no_street',
                'block_no'            => 'block_no',
                'post_code'           => 'post_code',
                'residence'           => 'residence',
                'landmark'            => 'landmark',
                'bank_account'        => 'bank_account',
                // 'franchise_pool'      => 'franchise_pool',
                'per_day_charges'     => 'per_day_charges',
                'royalty'             => 'royalty',
                'start_from_date'     => 'start_from_date',
                'fs_onoff'            => 'fs_on_off',
                'bank_pass_no'        => 'bank_pass_no',
                'bank_pass_front'     => 'bank_pass_front',
                'bank_pass_back'      => 'bank_pass_back',
                'statement_conduct'   => 'statement_conduct',
                'licence_front'       => 'licence_front',
                'licence_back'        => 'licence_back',
                'franchise_contract'  => 'franchise_contract',
                'extra_option'        => 'extra_option',
                'payroll_contract'    => 'payroll_contract',
                'franchise_number'    => 'franchise_number',
                'city'                => 'city',
                'country'             => 'country',
                'created_at'          => 'created_at',
                'updated_at'          => 'updated_at',
                'deleted_at'          => 'deleted_at',
            ]
        );

        // This is to separate the franchises.franchise_pool column into its own table (franchise_pool_table) and associate it with the franchises & pools table.
        $this->components->task('Migrate franchises.franchise_pool -> franchise_pool table.', function () {
            $pools = $this->oldConnection->table('franchises')->pluck('franchise_pool', 'franchise_id')->all();
            Franchise::all(['id', 'created_at'])
                ->each(function (Franchise $franchise) use ($pools) {
                    $franchise->pools()->attach(explode(',', $pools[$franchise->id]), ['created_at' => $franchise->created_at]);
                });
        });
    }

    protected function migrateMessagesTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'message',
            newTableName: (new Message)->getTable(),
            columns: [
                'message_id'     => 'id',
                'message_to'     => 'message_to',
                'message_user'   => 'message_user',
                'message_text'   => 'message_text',
                'message_status' => 'message_status',
                'image'          => 'image',
                'created_at'     => 'created_at',
                'updated_at'     => 'updated_at',
                'deleted_at'     => 'deleted_at',
            ]
        );
    }

    protected function migrateSettingsTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'settings',
            columns: [
                'id'            => 'id',
                'time_schedule' => 'time_schedule',
                'email'         => 'email',
                'address'       => 'address',
                'email_show'    => 'email_show',
                'contact_no'    => 'contact_no',
                'created_at'    => 'created_at',
                'updated_at'    => 'updated_at',
            ],
        );
    }

    protected function migrateWarehousesTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'warehouse',
            newTableName: (new Warehouse)->getTable(),
            columns: [
                'wh_id'       => 'id',
                'wh_name'     => 'wh_name',
                'wh_logo'     => 'wh_logo',
                'wh_minprice' => 'wh_minprice',
                'created_at'  => 'created_at',
                'updated_at'  => 'updated_at',
                'deleted_at'  => 'deleted_at',
            ]
        );
    }

    /**
     * Can take more than 10 seconds. Skip where f_id(franchise_id) doesn't exists
     */
    protected function migrateStockOrdersTable(): void
    {
        $franchiseIds = Franchise::withTrashed()->whereIn('id', $this->oldConnection->table('stock_order')->pluck('f_id'))->pluck('id');
        $oldIds = $this->oldConnection->table('stock_order')->whereIn('f_id', $franchiseIds)->pluck('f_id');
        $this->migrateOldTableToNewTable(
            oldTableName: 'stock_order',
            newTableName: (new StockOrder)->getTable(),
            columns: [
                'stock_orderid'        => 'id',
                'order_reference'      => 'order_reference',
                'f_id'                 => 'franchise_id',
                'order_status'         => 'order_status',
                'order_to'             => 'order_to',
                'order_type'           => 'order_type',
                'pickup_delivery_date' => 'pickup_delivery_date',
                'created_at'           => 'created_at',
                'updated_at'           => 'updated_at',
                'deleted_at'           => 'deleted_at',
            ],
            oldIds: $oldIds->all(),
            oldPrimaryKey: 'stock_orderid'
        );
    }

    protected function migrateDeliveryPeopleTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'deliveryperson',
            newTableName: (new DeliveryPerson)->getTable(),
            columns: [
                'dp_id'                   => 'id',
                'dp_name'                 => 'dp_name',
                'dp_email'                => 'dp_email',
                'dp_password'             => 'dp_password',
                'dp_contactno'            => 'dp_contact_no',
                'dp_street'               => 'dp_street',
                'dp_city'                 => 'dp_city',
                'dp_state'                => 'dp_state',
                'dp_postcode'             => 'dp_postcode',
                'dp_image'                => 'dp_image',
                'dp_device'               => 'dp_device',
                'dp_devicetoken'          => 'dp_device_token',
                'dp_hash'                 => 'dp_hash',
                'dp_lat'                  => 'dp_lat',
                'dp_lng'                  => 'dp_lng',
                'dp_onoff'                => 'dp_onoff',
                'dp_startodometer_number' => 'dp_start_odometer_number',
                'dp_stopodometer_number'  => 'dp_stop_odometer_number',
                'history_id'              => 'history_id',
                'bank_pass_no'            => 'bank_pass_no',
                'bank_pass_front'         => 'bank_pass_front',
                'bank_pass_back'          => 'bank_pass_back',
                'statement_conduct'       => 'statement_conduct',
                'licence_front'           => 'licence_front',
                'licence_back'            => 'licence_back',
                'franchise_contract'      => 'franchise_contract',
                'extra_option'            => 'extra_option',
                'payroll_contract'        => 'payroll_contract',
                'created_at'              => 'created_at',
                'updated_at'              => 'updated_at',
                'deleted_at'              => 'deleted_at',
            ]
        );
    }

    protected function migrateScheduleAbsenseTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'schedule_absense',
            newTableName: (new ScheduleAbsense)->getTable(),
            columns: [
                's_abid'       => 'id',
                'sa_dpid'      => 'delivery_person_id',
                'sa_starttime' => 'sa_start_time',
                'sa_endtime'   => 'sa_end_time',
                'updated_at'   => 'updated_at',
                'created_at'   => 'created_at',
                'deleted_at'   => 'deleted_at',
            ],
            resolveColumns: [
                'deleted_at' => fn ($oldRow) => $oldRow->deleted_at ? now()->createFromFormat('Y-m-d H:i:s', $oldRow->updated_at) : null,
            ],
            oldPrimaryKey: 's_abid'
        );
    }

    protected function migrateOrderStatusesTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'order_status',
            newTableName: (new OrderStatus)->getTable(),
            columns: [
                'os_id'    => 'id',
                'os_name'  => 'os_name',
                'os_color' => 'os_color',
            ]
        );
    }

    protected function migrateMessageUserTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'message_user',
            newTableName: (new MessageUser)->getTable(),
            columns: [
                'id'         => 'id',
                'm_id'       => 'message_id',
                'm_user'     => 'm_user',
                'm_userid'   => 'm_user_id',
                'updated_at' => 'updated_at',
                'created_at' => 'created_at',
                'deleted_at' => 'deleted_at',
            ]
        );
    }

    protected function migrateInvoicePdfsTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'invoicepdf_details',
            newTableName: (new InvoicePdf)->getTable(),
            columns: [
                'id'          => 'id',
                'orderId'     => 'order_id',
                'from_date'   => 'from_date',
                'to_date'     => 'to_date',
                'amount'      => 'amount',
                'paid_amount' => 'paid_amount',
                'f_id'        => 'franchise_id',
                'pdf_name'    => 'pdf_name',
                'created_at'  => 'created_at',
                'updated_at'  => 'updated_at',
                'deleted_at'  => 'deleted_at',
            ]
        );
    }

    /**
     * Skip where history_dpid(delivery_person_id) doesn't exists
     */
    protected function migrateDeliveryHistoriesTable(): void
    {
        $deliveryPersonIds = DeliveryPerson::withTrashed()->whereIn('id', $this->oldConnection->table('dp_history')->pluck('history_dpid'))->pluck('id');
        $oldIds = $this->oldConnection->table('dp_history')->whereIn('history_dpid', $deliveryPersonIds)->pluck('history_id');
        $this->migrateOldTableToNewTable(
            oldTableName: 'dp_history',
            newTableName: (new DeliveryHistory)->getTable(),
            columns: [
                'history_id'        => 'id',
                'history_dpid'      => 'delivery_person_id',
                'history_date'      => 'history_date',
                'history_starttime' => 'history_start_time',
                'history_endtime'   => 'history_end_time',
                'start_odometer'    => 'start_odometer',
                'end_odometer'      => 'end_odometer',
                'created_at'        => 'created_at',
                'updated_at'        => 'updated_at',
                'deleted_at'        => 'deleted_at',
            ],
            resolveColumns: [
                'history_endtime' => fn ($oldRow) => $oldRow->history_endtime == '0000-00-00 00:00:00' ? null : $oldRow->history_endtime,
            ],
            oldIds: $oldIds->all(),
            oldPrimaryKey: 'history_id',
        );
    }

    /**
     * Skip where dp_im_historyid(delivery_history_id) doesn't exists
     */
    protected function migrateDeliveryImagesTable(): void
    {
        $deliveryImageIds = DeliveryHistory::withTrashed()->whereIn('id', $this->oldConnection->table('dp_images')->pluck('dp_im_historyid'))->pluck('id');
        $oldIds = $this->oldConnection->table('dp_images')->whereIn('dp_im_historyid', $deliveryImageIds)->pluck('dp_im_id');
        $this->migrateOldTableToNewTable(
            oldTableName: 'dp_images',
            newTableName: (new DeliveryImage)->getTable(),
            columns: [
                'dp_im_id'        => 'id',
                'dp_im_historyid' => 'delivery_history_id',
                'dp_im_type'      => 'dp_im_type',
                'dp_im_name'      => 'dp_im_name',
                'created_at'      => 'created_at',
                'updated_at'      => 'updated_at',
            ],
            resolveColumns: [
                'updated_at' => fn ($oldRow) => $oldRow->created_at,
            ],
            oldIds: $oldIds->all()
        );
    }

    protected function migrateDeliveryTimeSchedulesTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'delivery_timeschedule',
            newTableName: (new DeliveryTimeSchedule)->getTable(),
            columns: [
                'id'          => 'id',
                'day'         => 'day',
                'start_time0' => 'start_time_0',
                'start_time1' => 'start_time_1',
                'end_time0'   => 'end_time_0',
                'end_time1'   => 'end_time_1',
                'is_checked'  => 'is_checked',
                'created_at'  => 'created_at',
                'updated_at'  => 'updated_at',
            ]
        );
    }

    /**
     * Skip where s_dpid(delivery_person_id) & s_fid(franchise_id) doesn't exists
     */
    protected function migrateSubDeliveryPeopleTable(): void
    {
        $deliveryPersonIds = DeliveryPerson::withTrashed()->whereIn('id', $this->oldConnection->table('deliveryperson_sub')->pluck('s_id'))->pluck('id');
        $fracnhiseIds = Franchise::withTrashed()->whereIn('id', $this->oldConnection->table('deliveryperson_sub')->pluck('s_fid'))->pluck('id');
        $oldIds = $this->oldConnection->table('deliveryperson_sub')
            ->whereIn('s_dpid', $deliveryPersonIds)
            ->whereIn('s_fid', $fracnhiseIds)
            ->pluck('s_pool', 's_id');
        $this->migrateOldTableToNewTable(
            oldTableName: 'deliveryperson_sub',
            newTableName: (new SubDeliveryPerson)->getTable(),
            columns: [
                's_id'       => 'id',
                's_dpid'     => 'delivery_person_id',
                's_fid'      => 'franchise_id',
                // 's_pool'     => 's_pool',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
                'deleted_at' => 'deleted_at',
            ],
            oldIds: $oldIds->keys()->all()
        );

        // This is to separate the sub_delivery_people.s_pool column into its own table (pool_sub_delivery_person) and associate it with the sub_delivery_people & pools table.
        $this->components->task('Migrate sub_delivery_people.s_pool -> pool_sub_delivery_person table.', function () use ($oldIds) {
            SubDeliveryPerson::all(['id', 'created_at'])
                ->each(function (SubDeliveryPerson $subDeliveryPerson) use ($oldIds) {
                    $subDeliveryPerson->pools()->attach(explode(',', $oldIds->all()[$subDeliveryPerson->id]), ['created_at' => $subDeliveryPerson->created_at]);
                });
        });
    }

    protected function migrateCustomerServiceHoursTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'customerservice_hours',
            newTableName: (new CustomerServiceHour)->getTable(),
            columns: [
                'h_id'            => 'id',
                'cs_id'           => 'customer_service_id',
                'start_date'      => 'start_date',
                'end_date'        => 'end_date',
                'start_time'      => 'start_time',
                'end_time'        => 'end_time',
                'per_hours'       => 'per_hours',
                'per_hours_inM'   => 'per_hours_in_minute',
                'total_hours_inM' => 'total_hours_in_minute',
                'total_hours_inH' => 'total_hours_in_hour',
                'created_at'      => 'created_at',
                'updated_at'      => 'updated_at',
                'deleted_at'      => 'deleted_at',
            ]
        );
    }

    protected function migrateContactUsTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'contactus',
            newTableName: (new ContactUs)->getTable(),
            columns: [
                'id'         => 'id',
                'name'       => 'name',
                'email'      => 'email',
                'contact_no' => 'contact_no',
                'subject'    => 'subject',
                'message'    => 'message',
                'to_send'    => 'to_send',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
                'deleted_at' => 'deleted_at',
            ]
        );
    }

    protected function migrateCmsPagesTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'cmspages',
            newTableName: (new CmsPage)->getTable(),
            columns: [
                '_id'                 => 'id',
                '_page_name'          => 'page_name',
                '_page_content_eng'   => 'page_content_eng',
                '_page_content_dutch' => 'page_content_dutch',
                'created_at'          => 'created_at',
                'updated_at'          => 'updated_at',
                // 'deleted_at' => 'deleted_at',
            ]
        );
    }

    protected function migrateChannelsTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'channel',
            newTableName: (new Channel)->getTable(),
            columns: [
                'id'            => 'id',
                'channel_id'    => 'channel_id',
                'channel_name'  => 'channel_name',
                'channel_image' => 'channel_image',
            ]
        );
    }

    protected function migrateRateAndReviewsTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'rate_review',
            newTableName: (new RateAndReview)->getTable(),
            columns: [
                'id'          => 'id',
                'order_id'    => 'order_id',
                'customer_id' => 'customer_id',
                'dp_id'       => 'delivery_person_id',
                'rate'        => 'rate',
                'review'      => 'review',
                'created_at'  => 'created_at',
                'updated_at'  => 'updated_at',
                'deleted_at'  => 'deleted_at',
            ]
        );
    }

    protected function migrateProductsTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'products',
            columns: [
                'product_id'            => 'id',
                'product_name'          => 'product_name',
                'product_articleNumber' => 'product_article_number',
                'product_type'          => 'product_type',
                'image'                 => 'image',
                'product_price'         => 'product_price',
                'vat'                   => 'vat',
                'vat_price'             => 'vat_price',
                'category_id'           => 'category_id',
                'description'           => 'description',
                // 'min_stock' => 'min_stock',
                // 'current_stock' => 'current_stock',
                // 'is_reminder_set' => 'is_reminder_set',
                'alcohol'    => 'alcohol',
                'order_from' => 'order_from',
                'is_popular' => 'is_popular',
                'is_show'    => 'is_show',
                // 'active_status' => 'active_status',
                'product_order'      => 'product_order',
                'api_availablestock' => 'api_available_stock',
                'main_price'         => 'main_price',
                'drank247_price'     => 'drank247_price',
                'customer_price'     => 'customer_price',
                'franchise_price'    => 'franchise_price',
                'alcoholic_items'    => 'alcoholic_items',
                'uber_product_type'  => 'product_type_id',
                'created_at'         => 'created_at',
                'updated_at'         => 'updated_at',
                'deleted_at'         => 'deleted_at',
            ],
            resolveColumns: [
                'category_id' => fn ($oldRow) => $oldRow->category_id == '' ? null : $oldRow->category_id,
            ]
        );
    }

    protected function migrateStocksTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'stocks',
            columns: [
                'stock_id'         => 'id',
                'stock_product'    => 'product_id',
                'stock_franchisee' => 'franchise_id',
                'stock_current'    => 'stock_current',
                'stock_minimum'    => 'stock_minimum',
                'max_stock_order'  => 'max_stock_order',
                'is_reminder_set'  => 'is_reminder_set',
                'created_at'       => 'created_at',
                'updated_at'       => 'updated_at',
                'deleted_at'       => 'deleted_at',
            ]
        );
    }

    protected function migrateCustomerServicesTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'customerservices',
            newTableName: (new CustomerService)->getTable(),
            columns: [
                'cs_id'              => 'id',
                'cs_name'            => 'cs_name',
                'cs_email'           => 'cs_email',
                'password'           => 'password',
                'cs_resettoken'      => 'cs_resettoken',
                'cs_mobileno'        => 'cs_mobileno',
                'cs_phone'           => 'cs_phone',
                'cs_street'          => 'cs_street',
                'cs_city'            => 'cs_city',
                'cs_state'           => 'cs_state',
                'cs_postcode'        => 'cs_postcode',
                'cs_image'           => 'cs_image',
                'cs_onoff'           => 'is_verified',
                'bank_pass_no'       => 'bank_pass_no',
                'bank_pass_front'    => 'bank_pass_front',
                'bank_pass_back'     => 'bank_pass_back',
                'statement_conduct'  => 'statement_conduct',
                'licence_front'      => 'licence_front',
                'licence_back'       => 'licence_back',
                'franchise_contract' => 'franchise_contract',
                'extra_option'       => 'extra_option',
                'payroll_contract'   => 'payroll_contract',
                'created_at'         => 'created_at',
                'updated_at'         => 'updated_at',
                'deleted_at'         => 'deleted_at',
            ]
        );
    }

    protected function migrateCustomersTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'customers',
            columns: [
                'customer_id'          => 'id',
                'google_id'            => 'google_id',
                'social_login_id'      => 'social_login_id',
                'login_type'           => 'login_type',
                'customer_resettoken'  => 'customer_reset_token',
                'customer_type'        => 'customer_type',
                'customer_from'        => 'customer_from',
                'customer_name'        => 'customer_name',
                'customer_email'       => 'customer_email',
                'customer_phone'       => 'customer_phone',
                'password'             => 'password',
                'profile'              => 'profile',
                'phone_code'           => 'phone_code',
                'customer_contactno'   => 'customer_contact_no',
                'customer_address'     => 'customer_address',
                'customer_devicetoken' => 'customer_device_token',
                'customer_device'      => 'customer_device',
                'customer_hash'        => 'customer_hash',
                'is_verified'          => 'is_verified',
                'created_at'           => 'created_at',
                'updated_at'           => 'updated_at',
                'deleted_at'           => 'deleted_at',
            ]
        );
    }

    protected function migrateCustomersadressTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'address',
            newTableName: (new CustomerAddress)->getTable(),
            columns: [
                'address_id'                 => 'id',
                'address_custid'             => 'customer_id',
                'address_address'            => 'address',
                'address_postcode'           => 'post_code',
                'address_latitude'           => 'latitude',
                'address_longitude'          => 'longitude',
                'address_default'            => 'default',
                'address_manual'             => 'manual',
                'address_houseno'            => 'house_no',
                'created_at'                 => 'created_at',
                'updated_at'                 => 'updated_at',
                'deleted_at'                 => 'deleted_at',
            ]
        );
    }

    protected function migratecardTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'cart',
            newTableName: (new Cart)->getTable(),
            columns: [
                'cart_id'        => 'id',
                'cart_custid'    => 'customer_id',
                'cart_itemid'    => 'product_id',
                'cart_qty'       => 'qty',
                'cart_itemprice' => 'item_price',
                'cart_total'     => 'total',
                'cart_vatprice'  => 'vat_price',
                'cart_vattotal'  => 'vat_total',
                'created_at'     => 'created_at',
                'updated_at'     => 'updated_at',
                'deleted_at'     => 'deleted_at',

            ]
        );
    }

    protected function migrateFavouritesTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'favourite',
            newTableName: (new Favourite)->getTable(),
            columns: [
                'fav_id'     => 'id',
                'fav_custid' => 'customer_id',
                'fav_itemid' => 'product_id',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
                'deleted_at' => 'deleted_at',
            ]
        );
    }

    protected function migratePromoCodesTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'promocode',
            newTableName: (new PromoCode)->getTable(),
            columns: [
                'code_id'         => 'id',
                'code_text'       => 'code_text',
                'discount_type'   => 'discount_type',
                'discount'        => 'discount',
                'limitation_type' => 'limitation_type',
                'max_users'       => 'max_users',
                'max_peruser'     => 'max_per_user',
                'expiration_type' => 'expiration_type',
                'start_date'      => 'start_date',
                'end_date'        => 'end_date',
                'code_status'     => 'code_status',
                'created_at'      => 'created_at',
                'updated_at'      => 'updated_at',
                'deleted_at'      => 'deleted_at',
            ]
        );
    }

    protected function migrateAllergensTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'allergen',
            newTableName: (new Allergen)->getTable(),
            columns: [
                'allergen_id'      => 'id',
                'name'             => 'name',
                'deliverect_value' => 'deliverect_value',
                'created_at'       => 'created_at',
                'updated_at'       => 'updated_at',
                'deleted_at'       => 'deleted_at',
            ]
        );
    }

    /**
     * Skip where deleted_at is null & allergen_id doesn't exists
     */
    protected function migrateAllergenProductTable(): void
    {
        $allergenIds = Allergen::withTrashed()->whereIn('id', $this->oldConnection->table('assign_allergen')->pluck('allergen_id'))->pluck('id');
        $oldIds = $this->oldConnection->table('assign_allergen')->whereIn('allergen_id', $allergenIds)->whereNull('deleted_at')->pluck('id');
        $this->migrateOldTableToNewTable(
            oldTableName: 'assign_allergen',
            newTableName: 'allergen_product',
            columns: [
                'allergen_id' => 'allergen_id',
                'product_id'  => 'product_id',
                'created_at'  => 'created_at',
            ],
            oldIds: $oldIds->all(),
            oldPrimaryKey: 'id'
        );
    }

    /**
     * Skip where assign_proid(product_id) doesn't exists
     */
    protected function migrateCategoryProductTable(): void
    {
        $productIds = Product::withTrashed()->whereIn('id', $this->oldConnection->table('assign_product')->pluck('assign_proid'))->pluck('id');
        $oldIds = $this->oldConnection->table('assign_product')->whereIn('assign_proid', $productIds)->pluck('assign_id');
        $this->migrateOldTableToNewTable(
            oldTableName: 'assign_product',
            newTableName: 'category_product',
            columns: [
                'assign_proid' => 'product_id',
                'assign_catid' => 'category_id',
                'created_at'   => 'created_at',
            ],
            oldIds: $oldIds->all(),
            oldPrimaryKey: 'assign_id',
        );
    }

    protected function migrateBannersTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'banner',
            newTableName: (new Banner)->getTable(),
            columns: [
                '_id'        => 'id',
                'image'      => 'image',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ]
        );
    }

    protected function migrateSchedulesTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'schedule',
            newTableName: (new Schedule)->getTable(),
            columns: [
                's_id'           => 'id',
                's_dpid'         => 'delivery_person_id',
                's_fid'          => 'franchise_id',
                's_time'         => 'time',
                's_startdate'    => 'start_date',
                's_enddate'      => 'end_date',
                's_pool'         => 'pool_id',
                's_status'       => 'status',
                's_approvedtime' => 'approved_time',
                'created_at'     => 'created_at',
                'updated_at'     => 'updated_at',
                'deleted_at'     => 'deleted_at',
            ]
        );
    }

    /**
     * Skip where order_franchiseid(franchise_id) doesn't exists (except zero). If order_franchiseid/od_deliverypersonid/order_promocode/order_payment_status are zero, set those to null
     */
    protected function migrateOrdersTable(): void
    {
        $deliveryPersonIds = DeliveryPerson::withTrashed()->whereIn('id', $this->oldConnection->table('orders')->pluck('od_deliverypersonid'))->pluck('id')->push(0);

        /** @var \Illuminate\Support\Collection<int, string> $channelsIds */
        $channelsIds = Channel::whereIn('channel_id', $this->oldConnection->table('orders')->pluck('order_channel_id'))->pluck('id', 'channel_id');
        $oldIds = $this->oldConnection->table('orders')
            ->whereIn('od_deliverypersonid', $deliveryPersonIds)
            ->whereIn('order_channel_id', $channelsIds->keys()->push(''))
            ->pluck('order_id');
        $this->migrateOldTableToNewTable(
            oldTableName: 'orders',
            columns: [
                'order_id'                   => 'id',
                'order_uuid'                 => 'uuid',
                'order_store_id'             => 'order_store_id',
                'order_uber_id'              => 'order_uber_id',
                'order_uber_display_id'      => 'order_uber_display_id',
                'order_takeaway_id'          => 'order_takeaway_id',
                'order_takeaway_key'         => 'order_takeaway_key',
                'order_takeaway_public_ref'  => 'order_takeaway_public_ref',
                'order_deliverect_id'        => 'order_deliverect_id',
                'order_channel_id'           => 'channel_id',
                'order_channelorder_id'      => 'order_channel_order_id',
                'channel_link'               => 'channel_link',
                'order_receiptid'            => 'order_receipt_id',
                'order_franchiseid'          => 'franchise_id',
                'order_customerid'           => 'customer_id',
                'order_approve'              => 'order_approve',
                'od_deliverypersonid'        => 'delivery_person_id',
                'order_addressid'            => 'order_address_id',
                'order_note'                 => 'order_note',
                'order_price'                => 'order_price',
                'order_deliverycharge'       => 'order_delivery_charge',
                'order_servicecharge'        => 'order_service_charge',
                'order_finalamount'          => 'order_final_amount',
                'order_discount'             => 'order_discount',
                'order_finalwithdiscount'    => 'order_final_with_discount',
                'order_promocode'            => 'promo_code_id',
                'order_status'               => 'order_status',
                'order_cancelledreason'      => 'order_cancelled_reason',
                'order_payment'              => 'order_payment',
                'order_payment_status'       => 'order_payment_status',
                'payment_method'             => 'payment_method',
                'service_fee'                => 'service_fee',
                'created_at'                 => 'created_at',
                'updated_at'                 => 'updated_at',
                'deleted_at'                 => 'deleted_at',
                'identity_entrance_code'     => 'identity_entrance_code',
                'identity_transaction_id'    => 'identity_transaction_id',
                'qr_id'                      => 'qr_id',
                'merchant_reference'         => 'merchant_reference',
                'iban_entrance_code'         => 'iban_entrance_code',
                'iban_transaction_id'        => 'iban_transaction_id',
                'od_assignedtime'            => 'od_assigned_time',
                'od_starttime'               => 'od_start_time',
                'od_endtime'                 => 'od_end_time',
                'failed_reason'              => 'failed_reason',
                'rejected_reason'            => 'rejected_reason',
                'od_rejectedid'              => 'od_rejected_id',
                'order_deliverytime'         => 'order_delivery_time',
                'uber_order_delivery_type'   => 'uber_order_delivery_type',
                'uber_order_delivery_status' => 'uber_order_delivery_status',
            ],
            resolveColumns: [
                'order_channel_id'     => fn ($oldRow) => $oldRow->order_channel_id ? $channelsIds[$oldRow->order_channel_id] : null,
                'order_franchiseid'    => fn ($oldRow) => $oldRow->order_franchiseid == 0 ? null : $oldRow->order_franchiseid,
                'order_addressid'      => fn ($oldRow) => $oldRow->order_addressid == 0 ? null : $oldRow->order_addressid,
                'od_deliverypersonid'  => fn ($oldRow) => $oldRow->od_deliverypersonid == 0 ? null : $oldRow->od_deliverypersonid,
                'order_promocode'      => fn ($oldRow) => $oldRow->order_promocode == 0 ? null : $oldRow->order_promocode,
                'order_payment_status' => fn ($oldRow) => $oldRow->order_payment_status == 'YES' ? true : false,
            ],
            oldIds: $oldIds->all(),
        );
    }

    /**
     * Skip where fs_order_id(order_id) & fs_fr_id(franchise_id) & fs_wh_id(warehouse_id) & fs_product_id(product_id) doesn't exists
     */
    protected function migrateFranchiseStockOrdersTable(): void
    {
        $orderIds = Order::withTrashed()->whereIn('id', $this->oldConnection->table('franchise_stock_order')->pluck('fs_order_id'))->pluck('id');
        $warehourseIds = Warehouse::withTrashed()->whereIn('id', $this->oldConnection->table('franchise_stock_order')->select('fs_wh_id'))->pluck('id');
        $fracnhiseIds = Franchise::withTrashed()->whereIn('id', $this->oldConnection->table('franchise_stock_order')->select('fs_fr_id'))->pluck('id');
        $productIds = Product::withTrashed()->whereIn('id', $this->oldConnection->table('franchise_stock_order')->pluck('fs_product_id'))->pluck('id');
        $oldIds = $this->oldConnection->table('franchise_stock_order')->whereIn('fs_order_id', $orderIds)
            ->whereIn('fs_wh_id', $warehourseIds)
            ->whereIn('fs_fr_id', $fracnhiseIds)
            ->whereIn('fs_product_id', $productIds)
            ->pluck('fs_id');
        $this->migrateOldTableToNewTable(
            oldTableName: 'franchise_stock_order',
            newTableName: (new FranchiseStockOrder)->getTable(),
            columns: [
                'fs_id'         => 'id',
                'fs_order_id'   => 'order_id',
                'fs_wh_id'      => 'warehouse_id',
                'fs_fr_id'      => 'franchise_id',
                'fs_product_id' => 'product_id',
                'fs_qty'        => 'fs_qty',
                'order_status'  => 'order_status',
                'created_at'    => 'created_at',
                'updated_at'    => 'updated_at',
                'deleted_at'    => 'deleted_at',
            ],
            oldIds: $oldIds->all()
        );
    }

    /**
     * Skip where d_id(delivery_person_id) & order_id(order_id) doesn't exists
     */
    protected function migrateHelpsTable(): void
    {
        $deliveryPersonIds = DeliveryPerson::withTrashed()->whereIn('id', $this->oldConnection->table('help')->pluck('d_id'))->pluck('id');
        $orderIds = Order::withTrashed()->whereIn('id', $this->oldConnection->table('help')->pluck('order_id'))->pluck('id');
        $oldIds = $this->oldConnection->table('help')->whereIn('d_id', $deliveryPersonIds)->whereIn('order_id', $orderIds)->pluck('id');

        $this->migrateOldTableToNewTable(
            oldTableName: 'help',
            newTableName: (new Help)->getTable(),
            columns: [
                'id'         => 'id',
                'type'       => 'type',
                'to_id'      => 'to_id',
                'd_id'       => 'delivery_person_id',
                'order_id'   => 'order_id',
                'message'    => 'message',
                'status'     => 'order_status_id',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
                'deleted_at' => 'deleted_at',
            ],
            oldIds: $oldIds->all(),
        );
    }

    /**
     * Can take more than 10 seconds.
     *
     * Skip where od_orderid(order_id) doesn't exists
     */
    protected function migrateOrderDetailsTable(): void
    {
        $orderIds = Order::withTrashed()->whereIn('id', $this->oldConnection->table('order_details')->pluck('od_orderid'))->pluck('id');
        $oldIds = $this->oldConnection->table('order_details')->whereIn('od_orderid', $orderIds)->pluck('od_id');
        $this->migrateOldTableToNewTable(
            oldTableName: 'order_details',
            columns: [
                'od_id'           => 'id',
                'od_orderid'      => 'order_id',
                'od_productid'    => 'product_id',
                'od_qty'          => 'od_qty',
                'od_itemprice'    => 'od_item_price',
                'od_total'        => 'od_total',
                'od_vatprice'     => 'od_vat_price',
                'od_vattotal'     => 'od_vat_total',
                'product_details' => 'product_details',
                'created_at'      => 'created_at',
                'updated_at'      => 'updated_at',
                'deleted_at'      => 'deleted_at',
            ],
            oldIds: $oldIds->all(),
            resolveColumns: [
                'od_productid' => fn ($oldRow) => $oldRow->od_productid == 0 ? Product::query()->where('product_article_number', json_decode($oldRow->product_details, true)['id'])->first()?->id : $oldRow->od_productid,
            ],
        );
    }

    protected function migrateStockProductsTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'stock_product',
            newTableName: (new StockProduct)->getTable(),
            columns: [
                '_id'            => 'id',
                '_name'          => 'name',
                '_price'         => 'price',
                '_description'   => 'description',
                '_articleNumber' => 'article_number',
                '_alcohol'       => 'alcohol',
                'created_at'     => 'created_at',
                'updated_at'     => 'updated_at',
                'deleted_at'     => 'deleted_at',
            ]
        );
    }

    /**
     * Skip where order_id doesn't exists
     */
    protected function migrateStockOrderDetailsTable(): void
    {
        $stockOrderIds = StockOrder::withTrashed()->whereIn('id', $this->oldConnection->table('stock_orderdetail')->pluck('order_id'))->pluck('id');
        $oldIds = $this->oldConnection->table('stock_orderdetail')->whereIn('order_id', $stockOrderIds)->pluck('_id');
        $this->migrateOldTableToNewTable(
            oldTableName: 'stock_orderdetail',
            newTableName: (new StockOrderDetail)->getTable(),
            columns: [
                '_id'        => 'id',
                'order_id'   => 'stock_order_id',
                'product_id' => 'product_id',
                'qty'        => 'qty',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
                'deleted_at' => 'deleted_at',
            ],
            oldIds: $oldIds->all()
        );
    }

    protected function migrateTakeawayTempsTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'takeaway_temp',
            newTableName: (new TakeawayTemp)->getTable(),
            columns: [
                'id'         => 'id',
                'order_id'   => 'order_id',
                'order_data' => 'order_data',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ]
        );
    }

    /**
     * Skip where order_id doesn't exists
     */
    protected function migrateOrderPaymentsTable(): void
    {
        $orderIds = Order::withTrashed()->whereIn('id', $this->oldConnection->table('tbl_order_payment')->pluck('order_id'))->pluck('id');
        $oldIds = $this->oldConnection->table('tbl_order_payment')->whereIn('order_id', $orderIds)->pluck('order_payment_id');
        $this->migrateOldTableToNewTable(
            oldTableName: 'tbl_order_payment',
            newTableName: (new OrderPayment)->getTable(),
            columns: [
                'order_payment_id'               => 'id',
                'order_id'                       => 'order_id',
                'identity_entrance_code'         => 'identity_entrance_code',
                'identity_transaction_id'        => 'identity_transaction_id',
                'identity_transaction_url'       => 'identity_transaction_url',
                'paymentid'                      => 'payment_id',
                'identity_transaction_short_url' => 'identity_transaction_short_url',
                'iban_entrance_code'             => 'iban_entrance_code',
                'iban_transaction_id'            => 'iban_transaction_id',
                'iban_transaction_url'           => 'iban_transaction_url',
                'iban_transaction_short_url'     => 'iban_transaction_short_url',
                'iban_refrence_id'               => 'iban_refrence_id',
                'iban_debtorrefrence_id'         => 'iban_debtorrefrence_id',
                'payment_status'                 => 'payment_status',
                'order_key'                      => 'order_key',
                'status_code'                    => 'status_code',
                'payment_method'                 => 'payment_method',
                'created_at'                     => 'created_at',
                'updated_at'                     => 'updated_at',
            ],
            oldIds: $oldIds->all()
        );
    }

    protected function migratePaymentMethodsTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'tbl_payment_method',
            newTableName: (new PaymentMethod)->getTable(),
            columns: [
                'id'          => 'id',
                'method_name' => 'method_name',
                'status'      => 'status',
                'created_at'  => 'created_at',
                'updated_at'  => 'updated_at',
            ]
        );
    }

    protected function migrateUbersTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'uber',
            newTableName: (new Uber)->getTable(),
            columns: [
                'id'         => 'id',
                'ordeR_id'   => 'order_id',
                'data'       => 'data',
                'created_at' => 'created_at',
            ]
        );
    }

    protected function migrateUberStoresTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'uber_stores',
            columns: [
                'id'             => 'id',
                'name'           => 'name',
                'store_id'       => 'store_id',
                'location'       => 'location',
                'status'         => 'status',
                'contact_emails' => 'contact_emails',
                'store_menu'     => 'store_menu',
                'created_at'     => 'created_at',
                'updated_at'     => 'updated_at',
                'deleted_at'     => 'deleted_at',
            ]
        );
    }

    protected function migrateUserPromoCodesTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'used_promocode',
            newTableName: (new UsedPromoCode)->getTable(),
            columns: [
                'u_id'       => 'id',
                'pcode_id'   => 'promo_code_id',
                'c_id'       => 'customer_id',
                'used_count' => 'used_count',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ]
        );
    }

    protected function migrateNotificationsTable(): void
    {
        $this->migrateOldTableToNewTable(
            oldTableName: 'notification',
            newTableName: (new Notification)->getTable(),
            columns: [
                'nt_id'      => 'id',
                'nt_usertype'=> 'user_type',
                'nt_toid'    => 'to_id',
                'nt_text'    => 'text',
                'nt_status'  => 'status',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
        );
    }

    protected function setCredentialFranchiseForLogin(): void
    {
        $this->components->task('(DEVELOPMENT) Set franchises password to password for login.',
            fn () => DB::table((new Franchise)->getTable())->update(['password' => Hash::make('password')]));
        $this->components->task('(DEVELOPMENT) Set franchises email invoice@247drank.nl unique for login.',
            fn () => Franchise::query()->where('franchises_email', 'invoice@247drank.nl')->get()->each(fn (Franchise $franchise) => $franchise->update(['franchises_email' => str()->random(3).'invoice@247drank.nl'])));
    }

    protected function setCredentialCustomerServiceForLogin(): void
    {
        $this->components->task('(DEVELOPMENT) Set customer services password to password for login.',
            fn () => DB::table((new CustomerService)->getTable())->update(['password' => Hash::make('password')]));
    }
}
