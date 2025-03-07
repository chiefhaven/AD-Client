<?php

namespace App\Http\Controllers;


use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PDFController extends Controller
{
    public function generatePdf()
    {
        $data = ['name' => 'John Doe'];
        $pdf = Pdf::loadView('pdf.example', $data);
        return $pdf->download('example.pdf'); // Displays in browser
    }
}