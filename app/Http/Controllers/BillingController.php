<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use PDF;

class BillingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
    {
        // // Fetch all billings with their related orders
        // $billings = Billing::with(['orders'])->get();

        // // Initialize the total amount
        // $total = 0;

        // // Calculate totals for each billing record
        // foreach ($billings as $billing) {
        //     $subtotal = $billing->orders->sum(function ($order) {
        //         $itemTotal = $order->quantity * $order->rate;

        //         // Apply tax if available
        //         if (isset($order->tax)) {
        //             $itemTotal += ($itemTotal * ($order->tax / 100));
        //         }

        //         return $itemTotal;
        //     });

        //     // Calculate total by applying discount
        //     // $billing->total = $subtotal - ($subtotal * ($billing->discount / 100));

        //     // Add the current billing's total to the overall total
        //     $total += $billing->total;  // Calculate the overall total
        // }

        $bills = Billing::with('products', 'payments')->where('client_id', Auth::user()->client_id)->get();

        // Pass both billings and total to the view
        return view('billings.billing', compact('bills'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Only admins are allowed to create and store Bills

        //     $billing = Billing::create([
        //     'client_id' => $request->client_id,
        //     'bill_type' => $request->bill_type,
        //     'total_amount' => $request->total_amount,
        //     'discount' => $request->discount,
        //     'paid_amount' => $request->paid_amount,
        //     'status' => $request->status,
        //     'balance' => $request->balance,
        //     'tax_amount' => $request->tax_amount,
        //     'discount_type' => $request->discount_type,
        //     'transaction_terms' => $request->transaction_terms,
        //     'description' => $request->description,
        //     'issue_date' => $request->issue_date,
        //     'due_date' => $request->due_date,
        // ]);

        // // Loop through products and create an order for each product
        // foreach ($request->products as $product) {
        //     $billing->orders()->create([
        //         'product_id' => $product['id'],
        //         'quantity' => $product['quantity'],
        //         'rate' => $product['rate'],
        //         'total' => $product['quantity'] * $product['price'],
        //     ]);
        // }

        // return redirect()->route('billings.index');

    }

    /**
     * Display the specified resource.
     */
   public function show(string $id)
    {
        // // Fetch the billing record along with related orders and client
        // $billing = Billing::with(['orders', 'client'])->findOrFail($id);

        // // Calculate subtotal: only quantity * rate with tax included
        // $subtotal = $billing->orders->sum(function ($order) {
        //     $itemTotal = $order->quantity * $order->rate;

        //     // Apply tax if available
        //     $itemTotal += ($itemTotal * (($order->tax ?? 0) / 100));

        //     return $itemTotal;
        // });

        // // Calculate total: subtotal with discount applied
        // $total = $subtotal;
        // $total -= ($total * (($billing->discount ?? 0) / 100)); // Apply discount if available

        // // Determine which view to return based on billing type
        // if ($billing->bill_type === 'invoice') {
        //     return view('billings.billingView', compact('billing'));
        // } elseif ($billing->bill_type === 'quotation') {
        //     return view('billings.billingViewQuotation', compact('billing'));
        // }

        // return redirect()->back()->with('error', 'Billing type not recognized.');

        try {
            // Find the bill and include related payments and products
            $bill = Billing::with(['payments', 'products', 'client'])->findOrFail($id);

            return response()->json($bill, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Bill not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching bill details.', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        return view('Billings.billingEdit');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function billPdf(Billing $billing, $id, $action)
    {
        // Use the injected $billing model directly, no need to call find()
        $bill = $billing::with('products')->find($id);

        // If no bill is found, handle the error
        if (!$bill) {
            return abort(404, 'Bill not found');
        }

        // Ensure the property name is correct (assuming it's 'invoice_number')
        $pdf = PDF::loadView('pdf.billsDefault', ['bill' => $bill]);

        if ($action == 'print') {
            // Return the PDF as a downloadable file
            return $pdf->stream($bill->invoice_number . '.pdf');
        } else {
            // Stream the PDF to the browser
            return $pdf->download($bill->invoice_number . '.pdf');
        }
    }

    public function downloadQuotation($id)
    {
        // Get the billing data for the invoice
        $billing = Billing::findOrFail($id);

        // Load the view and pass the billing data
        $pdf = PDF::loadView('billings.quotation_pdf', compact('billing'));

        // Set the filename
        $filename = 'Quotation_' . $billing->id . '.pdf';

        // Return the PDF download response
        return $pdf->download($filename);
    }



}
