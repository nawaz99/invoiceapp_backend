<?php

namespace App\Mail;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $gstRate;

    public function __construct(Invoice $invoice, $gstRate = 18)
    {
        $this->invoice = $invoice;
        $this->gstRate = $gstRate;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $this->invoice,
            'gstRate' => $this->gstRate
        ]);

        return $this->subject('Your Invoice')
            ->view('emails.invoice')
            ->with([
                'invoice' => $this->invoice,
                'gstRate' => $this->gstRate
            ])
            ->attachData($pdf->output(), 'invoice-' . $this->invoice->invoice_number . '.pdf');
    }
}
