<?php
namespace App\Services;
use App\Models\BookOrder;
use Illuminate\Support\Facades\Storage;
use setasign\FpdiProtection\FpdiProtection;

class BookPdfDeliveryService {
    public function secure(BookOrder $order): string {
        if ($order->secured_document_path) return $order->secured_document_path;
        $source=Storage::disk('local')->path($order->book->document_path); $pdf=new FpdiProtection('P', 'mm', 'A4', true);
        $pdf->setProtection([], $order->unlock_code, bin2hex(random_bytes(24)));
        $pages=$pdf->setSourceFile($source);
        for($page=1;$page<=$pages;$page++){ $tpl=$pdf->importPage($page); $size=$pdf->getTemplateSize($tpl); $pdf->AddPage($size['orientation'],[$size['width'],$size['height']]); $pdf->useTemplate($tpl); }
        $path='book-orders/'.$order->reference.'.pdf'; Storage::disk('local')->put($path,$pdf->Output('S'));
        $order->update(['secured_document_path'=>$path]); return $path;
    }
}
