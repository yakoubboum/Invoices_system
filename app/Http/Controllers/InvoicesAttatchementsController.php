<?php

namespace App\Http\Controllers;

use App\Models\invoices_attatchements;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoicesAttatchementsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {



        // $imageName = $request->file('file_name');

        // $filename=$imageName->getClientOriginalName();

        // $attachments = new invoice_attatchements();
        // $attachments->file_name = $filename;
        // $attachments->invoice_number = $request->invoice_number;
        // $attachments->Created_by = Auth::user()->name;
        // $attachments->invoice_id = $request->invoice_id;
        // $attachments->save();




        // $request->file_name->move(public_path('Attachments/' . $request->invoice_number), $imageName);

        // session()->flash('add', 'تم اضافة المرفق بنجاح');
        // return back();


        $image = $request->file('file_name');
        $file_name = $image->getClientOriginalName();

        $attachments =  new invoices_attatchements();
        $attachments->file_name = $file_name;
        $attachments->invoice_number = $request->invoice_number;
        $attachments->invoice_id = $request->invoice_id;
        $attachments->Created_by = Auth::user()->name;
        $attachments->save();

        // move pic
        $imageName = $request->file_name->getClientOriginalName();
        $request->file_name->move(public_path('Attachments/'. $request->invoice_number), $imageName);

        session()->flash('Add', 'تم اضافة المرفق بنجاح');
        return back();

    }

    /**
     * Display the specified resource.
     */
    public function show(invoices_attatchements $invoices_attatchements)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(invoices_attatchements $invoices_attatchements)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, invoices_attatchements $invoices_attatchements)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(invoices_attatchements $invoices_attatchements)
    {

    }
}
