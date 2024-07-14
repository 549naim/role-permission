<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\JsonResponse;

use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login', function (): JsonResponse {
    // return view('welcome');
    // $response = new JsonResponse();
    // return  $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
    return response()->json([
        'status' => 'Error',
        'message' => 'Unauthorised'
    ], 401);
})->name('login');

// Route::get('/get1', function () {
//     $products = DB::connection('training')->table("sso_user_profile")->get();   
//     dd($products);
// });


// // Route::get('/get2', function () {
// //     $products = DB::connection('training2')->table("citizen_educations")->get();      
// //     dd($products);
// // });

// Route::get('/get3', function () {
//     $products = DB::connection('bsap_main')->table("my_gov_service")->get();     
//     dd($products);
// });




Route::get('/admin-login', [AdminController::class, 'admin_login'])->name('admin-login');
Route::post('/post_admin_login', [AdminController::class, 'post_admin_login'])->name('post_admin_login');

Route::group(['middleware' => ['auth']], function () {

    Route::get('/index', [AdminController::class, 'index']);
    Route::post('/logout', [AdminController::class, 'logout']);
    Route::get('/user-list', [AdminController::class, 'user_list'])->name('user-list');
    Route::get('/user-create', [AdminController::class, 'user_create'])->name('user-create');
    Route::get('/user-edit/{id}', [AdminController::class, 'user_edit'])->name('user-edit');
    Route::post('/user-store', [AdminController::class, 'user_store'])->name('user-store');
    Route::post('/user-update/{id}', [AdminController::class, 'user_update'])->name('user-update');
    Route::get('/userdelete/{id}', [AdminController::class, 'deleteUser'])->name('delete-user');

    Route::group([
        'namespace' => 'App\Http\Controllers',
    ], function () {
        Route::resource('roles', RoleController::class);
        Route::post('/create-role', ['uses' => 'RoleController@createRole'])->name('create-role');
        Route::post('/create-permission', ['uses' => 'RoleController@createPermission'])->name('create-permission');
        Route::get('/role_permission', ['uses' => 'RoleController@role_permission'])->name('role_permission');
        Route::get('/role-delete/{id}', ['uses' => 'RoleController@role_delete'])->name('role-delete');
        Route::get('/permission-delete/{id}', ['uses' => 'RoleController@permission_delete'])->name('permission-delete');
        Route::get('/role-table', ['uses' => 'RoleController@roleTable'])->name('role-table');
        Route::get('/permission-table', ['uses' => 'RoleController@PermissionTable'])->name('permission-table');

        Route::get('/role-edit/{id}', ['uses' => 'RoleController@role_edit'])->name('role-edit');
        Route::get('/permission-edit/{id}', ['uses' => 'RoleController@permission_edit'])->name('permission-edit');

        Route::post('/role-update', ['uses' => 'RoleController@role_update'])->name('role-update');
        Route::post('/permission-update', ['uses' => 'RoleController@permission_update'])->name('permission-update');

    });
});