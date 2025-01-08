<?php

use App\Http\Controllers\Api\Admin\DonorItemController;
use App\Http\Controllers\Api\Admin\DriverController;
use App\Http\Controllers\Api\Admin\FileController;
use App\Http\Controllers\Api\Admin\ItemController;
use App\Http\Controllers\Api\Admin\BranchController;
use App\Http\Controllers\Api\Admin\NotificationController;
use App\Http\Controllers\Api\Admin\TransactionItemController;
use App\Http\Controllers\Api\Admin\WarehouseItemController;
use App\Http\Controllers\Api\Admin\TransactionController;
//use App\Http\Controllers\Api\Admin\TransactionWarehouseItemController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\WarehouseController;
use App\Http\Controllers\Api\Keeper\WarehouseController as KeeperWarehouseController;
use App\Http\Controllers\Api\Keeper\ItemController as KeeperItemController;
use App\Http\Controllers\Api\Keeper\TransactionController as KeeperTransactionController;
use App\Http\Controllers\Api\Donor\TransactionController as DonorTransactionController;
use App\Http\Controllers\Api\Donor\DonorItemController as DonorItemForDonorController;
use App\Http\Controllers\Api\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

require_once __DIR__ . '/Api/Auth.php';


Route::middleware(['auth:sanctum', 'Localization'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware(['auth:sanctum', 'Localization'])->group(function () {

    Route::controller(SearchController::class)->prefix('search')->group(function () {
        Route::get('searchItems', 'searchItems')->name('items.search');
        Route::get('searchDrivers', 'searchDrivers')->name('drivers.search');
        Route::get('searchTransactions', 'searchTransactions')->name('transactions.search');
        Route::get('searchWarehouses', 'searchWarehouses')->name('warehouses.search');
        Route::get('searchWarehouses_items', 'searchWarehousesItems')->name('warehouses_items.search');
        Route::get('searchBranches', 'searchBranches')->name('branches.search');
        Route::get('searchUsers', 'searchUsers')->name('users.search');
    });
    // Admin Routes
    Route::controller(BranchController::class)->prefix('branches')->group(function () {
        Route::post('restore', 'restore');
        Route::get('showDeleted', 'showDeleted');
    });

    Route::controller(DriverController::class)->prefix('drivers')->group(function () {
        Route::post('restore', 'restore');
        Route::get('showDeleted', 'showDeleted');
    });

    Route::controller(ItemController::class)->prefix('items')->group(function () {
        Route::post('restore', 'restore');
        Route::patch('approveItem/{item}','approveItem');
        Route::get('showDeleted', 'showDeleted');
        Route::get('exportBySector/{sector}', 'exportBySector');
    });

    Route::controller(WarehouseController::class)->prefix('warehouses')->group(function () {
        Route::post('restore', 'restore');
        Route::get('showDeleted', 'showDeleted');
        Route::get('indexWarehouseWithItems', 'indexWarehouseWithItems');
        Route::get('showWarehouseWithItems/{warehouse}', 'showWarehouseWithItems');
        Route::get('showWarehouseOfKeeper/{keeper}', 'showWarehouseOfKeeper');
        Route::get('Export', 'exportAndSave');
    });

    Route::controller(TransactionController::class)->prefix('transactions')->group(function () {
        Route::post('restore', 'restore');
        Route::get('showDeleted', 'showDeleted');
        Route::get('InDeliveryExport', 'transactionInDeliveryExport');
        Route::get('CompletedExport', 'transactionCompletedExport');

    });

    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::post('restore', 'restore');
        Route::get('showDeleted', 'showDeleted');
        Route::get('keeperExport', 'keeperExport');
        Route::get('donorExport', 'donorExport');
        Route::get('allUsersExport', 'allUsersExport');
    });

    Route::controller(WarehouseItemController::class)->prefix('warehouseItems')->group(function () {
        Route::post('restore', 'restore');
        Route::get('showDeleted', 'showDeleted');
    });

    Route::controller(TransactionItemController::class)->prefix('transactionItems')->group(function () {
        Route::post('restore', 'restore');
        Route::get('showDeleted', 'showDeleted');
        Route::get('inventoryForWarehouse', 'inventoryForWarehouse');
        Route::get('exportInventory', 'exportInventory');
        Route::get('systemInventory', 'systemInventory');
    });

    Route::controller(DonorItemController::class)->prefix('donorItems')->group(function () {
        Route::post('restore', 'restore');
        Route::get('showDeleted', 'showDeleted');
    });

    // Keeper Routes
    Route::get('showWarehouseForKeeper', [KeeperWarehouseController::class, 'show']);
    Route::get('inventoryForKeeper', [KeeperWarehouseController::class, 'inventory']);
    Route::get('exportInventory', [KeeperWarehouseController::class, 'exportInventory']);
    Route::get('indexItemForKeeper', [KeeperItemController::class, 'index']);
    Route::get('showItemForKeeper/{item_id}', [KeeperItemController::class, 'show']);
    Route::get('indexTransactionForKeeper', [KeeperTransactionController::class, 'index']);
    Route::get('showTransactionForKeeper/{transaction_id}', [KeeperTransactionController::class, 'show']);
    Route::post('createKeeperTransaction', [KeeperTransactionController::class, 'store']);
    Route::get('keeper/files', [\App\Http\Controllers\Api\Keepe\FileController::class, 'index']);


    // Donor Routes
    Route::get('indexTransactionForDonor', [DonorTransactionController::class, 'index']);
    Route::get('showTransactionForDonor/{transaction_id}', [DonorTransactionController::class, 'show']);
    Route::post('createDonorTransaction', [DonorTransactionController::class, 'store']);
    Route::get('indexItemForDonor', [DonorItemForDonorController::class, 'index']);
    Route::get('showItemForDonor/{item_id}', [DonorItemForDonorController::class, 'show']);
    Route::post('createNewItem', [DonorItemForDonorController::class, 'store']);


    // API Resource Routes
    Route::apiResources([
        'drivers' => DriverController::class,
        'branches' => BranchController::class,
        'warehouses' => WarehouseController::class,
        'users' => UserController::class,
        'items' => ItemController::class,
        'warehouseItems' => WarehouseItemController::class,
        'transactions' => TransactionController::class,
        'transactionItems' => TransactionItemController::class,
        'donorItems' => DonorItemController::class,
    ]);
    Route::get('/files', [FileController::class, 'index']);
    Route::post('/files/downloader', [FileController::class, 'downloadFile']);
    Route::get('/notifications', [NotificationController::class, 'index']);
});
