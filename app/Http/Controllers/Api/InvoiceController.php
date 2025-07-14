<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceMail;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Invoice::with('client', 'items')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date',
            'items' => 'required|array',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0'
        ]);


        // Calculate subtotal and total
        $subtotal = collect($validated['items'])->sum(function ($item) {
            return $item['quantity'] * $item['unit_price'];
        });

        $tax = $subtotal * 0.18; // 18% GST
        $total = $subtotal + $tax;


        // $latestInvoice = Invoice::latest('id')->first();
        // $nextNumber = $latestInvoice ? $latestInvoice->id + 1 : 1;
        // $invoiceNumber = 'INV-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        $year = now()->format('Y');
        $lastInvoice = Invoice::whereYear('created_at', $year)->latest('id')->first();

        $nextNumber = 1;
        if ($lastInvoice && preg_match("/INV-{$year}-(\d+)/", $lastInvoice->invoice_number, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }

        $invoiceNumber = 'INV-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        // Create invoice
        $invoice = Invoice::create([
            'client_id' => $validated['client_id'],
            'invoice_number' =>  $invoiceNumber,
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'],
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'status' => 'draft'
        ]);

        // Create invoice items
        foreach ($validated['items'] as $item) {
            $itemTotal = $item['quantity'] * $item['unit_price'];
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $itemTotal
            ]);
        }

        return response()->json($invoice->load('items'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        return $invoice->load('client', 'items');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'invoice_date' => 'date',
            'due_date' => 'date',
            'status' => 'in:draft,sent,paid,overdue',
            // You can allow updating items too if needed
        ]);

        $invoice->update($validated);

        return response()->json($invoice->load('items'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return response()->json(['message' => 'Invoice deleted']);
    }

    public function download(Invoice $invoice)
    {
        $invoice->load('client', 'items');
        $gstRate = 18; // You can fetch this from settings/config later
        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'gstRate' => $gstRate
        ]);
        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function emailInvoice(Invoice $invoice)
    {
        $invoice->load('client', 'items');

        Mail::to($invoice->client->email)->send(new InvoiceMail($invoice));

        return response()->json(['message' => 'Invoice emailed successfully']);
    }
}
