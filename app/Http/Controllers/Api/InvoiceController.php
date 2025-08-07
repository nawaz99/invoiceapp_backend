<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceMail;
use App\Models\CompanySetting;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    /**
     * Show only invoices belonging to the logged-in user
     */
    public function index()
    {
        return Invoice::where('user_id', auth()->id())
            ->with('client', 'items')
            ->latest()
            ->get();
    }

    /**
     * Store a new invoice for the logged-in user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0'
        ]);

        // Ensure the client belongs to the logged-in user
        $client = auth()->user()->clients()->findOrFail($validated['client_id']);

        // Calculate totals
        $subtotal = collect($validated['items'])->sum(fn($item) => $item['quantity'] * $item['unit_price']);
        $tax = $subtotal * 0.18;
        $total = $subtotal + $tax;

        // Year-based invoice numbering but UNIQUE per user
        $year = now()->format('Y');

        // Get last invoice **for this user only**
        $lastInvoice = Invoice::where('user_id', auth()->id())
            ->whereYear('created_at', $year)
            ->latest('id')
            ->first();

        // Default invoice number counter
        $nextNumber = 1;

        if ($lastInvoice && preg_match("/INV-{$year}-(\d+)/", $lastInvoice->invoice_number, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }

        // Example format: U123-INV-2025-0001 (UserID prefix)
        $invoiceNumber = "U" . auth()->id() . "-INV-{$year}-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Create invoice with user_id
        $invoice = Invoice::create([
            'user_id' => auth()->id(),
            'client_id' => $validated['client_id'],
            'invoice_number' => $invoiceNumber,
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'],
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'status' => 'draft'
        ]);

        // Create invoice items
        foreach ($validated['items'] as $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price']
            ]);
        }

        return response()->json($invoice->load('items'), 201);
    }

    /**
     * Show single invoice (only if it belongs to the logged-in user)
     */
    public function show(Invoice $invoice)
    {
        if ($invoice->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return $invoice->load('client', 'items');
    }

    /**
     * Update invoice (only if it belongs to the logged-in user)
     */
    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:draft,sent,paid,overdue',
        ]);

        $invoice->update($validated);
        return response()->json(['message' => 'Invoice status updated', 'invoice' => $invoice]);
        // return response()->json($invoice->load('items'));s
    }

    /**
     * Delete invoice (only if it belongs to the logged-in user)
     */
    public function destroy(Invoice $invoice)
    {
        if ($invoice->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $invoice->delete();
        return response()->json(['message' => 'Invoice deleted']);
    }

    /**
     * Download invoice PDF (only if it belongs to the logged-in user)
     */
    public function download(Invoice $invoice)
    {
        if ($invoice->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $company = CompanySetting::where('user_id', $invoice->user_id)->first();
        $invoice->load('client', 'items', 'company');
        $gstRate = 18;
        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'gstRate' => $gstRate,
            'company' => $company,

        ]);

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Send invoice email (only if it belongs to the logged-in user)
     */
    public function emailInvoice(Invoice $invoice)
    {
        if ($invoice->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $invoice->load('client', 'items', 'user');
        Mail::to($invoice->client->email)->send(new InvoiceMail($invoice));

        return response()->json(['message' => 'Invoice emailed successfully']);
    }
}
