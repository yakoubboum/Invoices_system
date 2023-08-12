<?php

namespace App\Http\Controllers;

use App\Models\invoices;
use App\Models\sections;
use Illuminate\Http\Request;

class customersreport extends Controller
{
    public function index()
    {
        $sections = sections::all();
        return view('reports.customers_report', compact("sections"));
    }

    public function search(Request $request)
    {
        $sections = sections::all();
        if ($request->Section && $request->product && $request->start_at == '' && $request->end_at == '') {

            if($request->product=="الكل"){
                $invoices = invoices::select('*')->where('section_id', '=', $request->Section)->get();
                return view('reports.customers_report', compact('sections'))->withDetails($invoices);
            }


            $invoices = invoices::select('*')->where('section_id', '=', $request->Section)->where('product', '=', $request->product)->get();

            return view('reports.customers_report', compact('sections'))->withDetails($invoices);
        }


        // في حالة البحث بتاريخ

        else{

            $start_at = date($request->start_at);
            $end_at = date($request->end_at);

            if($request->start_at&&$request->end_at == '' ){
                if($request->product=="الكل"){
                    $invoices = invoices::where('invoice_Date','>=', $start_at)->where('section_id', '=', $request->Section)->get();
                    return view('reports.customers_report', compact('sections'))->withDetails($invoices);
                }
                $invoices = invoices::where('invoice_Date','>=', $start_at)->where('section_id', '=', $request->Section)->where('product', '=', $request->product)->get();
                return view('reports.customers_report', compact('sections'))->withDetails($invoices);
            }
            elseif($request->start_at== ''&&$request->end_at  ){
                if($request->product=="الكل"){
                    $invoices = invoices::where('invoice_Date','<=', $end_at)->where('section_id', '=', $request->Section)->get();
                    return view('reports.customers_report', compact('sections'))->withDetails($invoices);
                }
                $invoices = invoices::where('invoice_Date','<=', $end_at)->where('section_id', '=', $request->Section)->where('product', '=', $request->product)->get();
                return view('reports.customers_report', compact('sections'))->withDetails($invoices);
            }

            if($request->product=="الكل"){
                $invoices = invoices::whereBetween('invoice_Date', [$start_at, $end_at])->where('section_id', '=', $request->Section)->get();
                return view('reports.customers_report', compact('sections'))->withDetails($invoices);
            }

            $invoices = invoices::whereBetween('invoice_Date', [$start_at, $end_at])->where('section_id', '=', $request->Section)->where('product', '=', $request->product)->get();

            return view('reports.customers_report', compact('sections'))->withDetails($invoices);
        }
    }
}
