<?php

namespace App\Mail;

use App\Models\CompanySetting;
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

        $company = CompanySetting::where('user_id', $this->invoice->user_id)->first();
        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $this->invoice,
            'gstRate' => $this->gstRate,
            'company' => $company,

        ]);

        return $this->subject('Your Invoice')
            ->view('emails.invoice')
            ->with([
                'invoice' => $this->invoice,
                'gstRate' => $this->gstRate,
                'company' => $company,

            ])
            ->attachData($pdf->output(), 'invoice-' . $this->invoice->invoice_number . '.pdf');
    }
}
