<?php

namespace App\Http\Controllers;

use App\Exports\invoicesExport;


use App\Models\invoices;
use App\Models\invoices_attatchements;
use App\Models\invoices_details;
use App\Models\sections;
use App\Notifications\Invoicecreate;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


use Maatwebsite\Excel\Facades\Excel;



class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = invoices::all();
        return view("invoices.invoices", compact("invoices"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sections = sections::all();
        return view("invoices.add_invoice", compact("sections"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'invoice_number' => 'required|max:255|unique:invoices',
            'Section' => 'required|max:255',
        ], [
            'invoice_number.required' => "يرجى ادخال رقم الفاتورة",
            'Product_name.unique' => " رقم الفاتورة موجود بالفعل",
        ]);
        invoices::create([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
        ]);


        $invoice_id = invoices::latest()->first()->id;
        invoices_details::create([
            'id_Invoice' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'Section' => $request->Section,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);



        if ($request->hasFile('pic')) {

            $invoice_id = Invoices::latest()->first()->id;
            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;

            $attachments = new invoices_attatchements();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $invoice_number;
            $attachments->Created_by = Auth::user()->name;
            $attachments->invoice_id = $invoice_id;
            $attachments->save();

            // move pic
            $imageName = $request->pic->getClientOriginalName();
            $request->pic->move(public_path('Attachments/' . $request->invoice_number), $imageName);
        }

        //Error--------------------
        // $user = User::get();

        // Notification::send($user, new Invoicecreate($invoice_id));
        //--------------------------

        $user=User::where("name","!=",Auth::user()->name)->first();

        $invoice= invoices::latest()->first();




        Notification::send($user, new \App\Notifications\InvoiceN($invoice));



        session()->flash('Add', 'تم اضافة الفاتورة بنجاح');
        return back();
    }




    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $invoices = invoices::where('id', $id)->first();

        return view("invoices.update_status", compact("invoices"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $invoices = invoices::where('id', $id)->first();

        $sections = sections::where('id', '!=', $invoices->section_id)->get();



        return view("invoices.edit_invoice", compact("invoices", "sections"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {


        $old_folder_path = public_path() . '/' . 'Attachments/' . $request->invoice_old_number;
        $new_folder_path = public_path() . '/' . 'Attachments/' . $request->invoice_number;


        if (File::exists($old_folder_path)) {
            File::move($old_folder_path, $new_folder_path);
        }







        $invoice = invoices::findOrFail($request->invoice_id);


        $validate = $request->validate([
            'invoice_number' => 'required|max:255',
            'Section' => 'required|max:255',
            'product' => 'required|max:255',
        ], [
            'invoice_number.required' => "يرجى ادخال رقم الفاتورة",
            'Section.required' => "يرجى ادخال رقم الفاتورة",
            'product.required' => "يرجى ادخال رقم الفاتورة",

        ]);
        $invoice->update([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
        ]);

        $invoice_detail = invoices_details::where('id_Invoice', $request->invoice_id)->first();



        $invoice_detail->update([
            'id_Invoice' => $request->invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'Section' => $request->Section,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);

        $invoice_attachments = invoices_attatchements::where('invoice_id', $request->invoice_id)->get();

        foreach ($invoice_attachments as $invoice_attachment) {
            $invoice_attachment->invoice_number = $request->invoice_number;
            $invoice_attachment->save();
        }





        session()->flash('edit', 'تم تعديل الفاتورة بنجاح');
        return redirect("/home/invoices");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {

        $id = $request->invoice_id;
        $invoices = invoices::where('id', $id)->first();
        $Details = invoices_attatchements::where('invoice_id', $id)->first();




        if (!$request->id_page) {

            if (!empty($Details->invoice_number)) {

                Storage::disk('public_uploads')->deleteDirectory($Details->invoice_number);
            }

            $invoices->forceDelete();
            session()->flash('delete_invoice');
            return redirect('/home/invoices');
        } else {
            $invoices->delete();
            session()->flash('archive_invoice');
            return redirect('home/archive_invoices');
        }
    }

    public function getproducts($id)
    {
        $products = DB::table("products")->where("section_id", $id)->pluck("Product_name", "id");
        return json_encode($products);
    }

    public function Status_update(Request $request, $id)
    {

        $invoice = invoices::where('id', $id)->first();
        if ($request->Status == "مدفوعة") {

            $invoice->update([
                'Status' => $request->Status,
                'Value_Status' => 1,
                'Payment_Date' => $request->Payment_Date,
            ]);

            invoices_details::create([
                'id_Invoice' => $id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => $request->Status,
                'Value_Status' => 1,
                'note' => $request->note,
                'user' => (Auth::user()->name),
                'Payment_Date' => $request->Payment_Date,
            ]);
        } else {
            $invoice->update([
                'Status' => $request->Status,
                'Value_Status' => 3,
                'Payment_Date' => $request->Payment_Date,
            ]);

            invoices_details::create([
                'id_Invoice' => $id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => $request->Status,
                'Value_Status' => 3,
                'note' => $request->note,
                'user' => (Auth::user()->name),
                'Payment_Date' => $request->Payment_Date,
            ]);
        }

        session()->flash('Status_Update');
        return redirect('/home/invoices');
    }

    public function Paid_invoices()
    {
        $invoices = invoices::where('Value_Status', 1)->get();

        return view("invoices.invoices_paid", compact("invoices"));
    }

    public function Unpaid_invoices()
    {
        $invoices = Invoices::where('Value_Status', 2)->get();
        return view('invoices.invoices_unpaid', compact('invoices'));
    }

    public function Invoice_Partial()
    {
        $invoices = Invoices::where('Value_Status', 3)->get();
        return view('invoices.invoices_partial', compact('invoices'));
    }

    public function Invoice_archive()
    {
        $invoices = invoices::onlyTrashed()->get();

        return view("invoices.invoices_archive", compact("invoices"));
    }


    public function cancelarchive(Request $request)
    {

        $id = $request->invoice_id;

        $invoice = invoices::withTrashed()->where('id', $id)->restore();

        session()->flash('restore_invoice');

        return back();
    }

    public function Invoice_archive_destroy(Request $request)
    {
        $id = $request->invoice_id;

        $invoice = invoices::withTrashed()->where('id', $id);
        $invoice->forceDelete();
        session()->flash('delete_invoice');

        return back();
    }

    public function print_invoice($id)
    {
        $invoices = invoices::where('id', $id)->first();
        return view('invoices.Print_invoice', compact('invoices'));
    }

    public function export()
    {
        return Excel::download(new invoicesExport, 'users.xlsx');

    }


    public function MarkAsRead_all (Request $request)
    {



        
        $userUnreadNotification= auth()->user()->unreadNotifications;

        if($userUnreadNotification) {
            $userUnreadNotification->markAsRead();

        }
        return back();

    }
}
