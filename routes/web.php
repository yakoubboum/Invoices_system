<?php

use App\Http\Controllers\invoices_reportsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\customersreport;
use App\Http\Controllers\InvoicesAttatchementsController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\InvoicesDetailsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SectionsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

use Illuminate\Support\Facades\Auth;

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

Route::get('/', function () {
    return view('auth.login');
});





Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/{page}', [AdminController::class, "show"]);

Route::resource("home/invoices", InvoicesController::class);

Route::resource("home/sections", SectionsController::class);

Route::resource("/home/products", ProductsController::class);

Route::get("/home/invoices/getproducts/{id}", [InvoicesController::class, "getproducts"]);

Route::get('InvoicesDetails/{invoice_id}', [InvoicesDetailsController::class, "edit"]);

Route::get('InvoicesDetailswithread/{invoice_id}', [InvoicesDetailsController::class, "markasread"]);


Route::get('View_file/{invoice_number}/{file_name}', [InvoicesDetailsController::class, "openfile"]);


Route::get('get_file/{invoice_number}/{file_name}', [InvoicesDetailsController::class, "get_file"]);



Route::post('delete_file', [InvoicesDetailsController::class, "destroy"])->name('deletefile');



Route::resource("InvoiceAttachments", InvoicesAttatchementsController::class);

Route::get("Status_show/{id}", [InvoicesController::class, "show"])->name("Status_show");


Route::post("Status_update/{id}", [InvoicesController::class, "Status_update"])->name("Status_Update");


Route::get("home/paidinvoices", [InvoicesController::class, "Paid_invoices"]);

Route::get("home/Unpaid_invoices", [InvoicesController::class, "Unpaid_invoices"]);

Route::get("home/partial_invoices", [InvoicesController::class, "Invoice_Partial"]);


Route::get("home/archive_invoices", [InvoicesController::class, "Invoice_archive"]);


Route::post("home/goto_invoices", [InvoicesController::class, "cancelarchive"])->name("Archive.update");

Route::delete("Invoice_archive_destroy", [InvoicesController::class, "Invoice_archive_destroy"])->name("Archive.destroy");;

Route::get("/print_invoice/{id}", [InvoicesController::class, "print_invoice"]);


Route::get('/invoices/exportinvoices', [InvoicesController::class, 'export']);





Route::resource('home/roles', RoleController::class)->middleware('auth');

Route::resource('home/users', UserController::class)->middleware('auth');


Route::get('home/reports', [invoices_reportsController::class, 'index']);

Route::post('home/reports/search', [invoices_reportsController::class, 'Search_invoices']);

Route::get('home/customersreports', [customersreport::class, 'index']);


Route::post('home/customersreports/search', [customersreport::class, 'search']);

Route::get('home/mark_all_as_read', [InvoicesController::class, 'MarkAsRead_all']);
