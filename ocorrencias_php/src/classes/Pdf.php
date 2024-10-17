<?php

use \Mpdf\Mpdf;

class Pdf {

    private $pdf;
    private $html;
    private $path;

    public function __construct($template, $acao = '', $formato = 'A4', $orientacao = 'P', $modo = 'utf-8')
    {
        $this->pdf = new Mpdf([
            "format"      => $formato,
            "orientation" => $orientacao,
            "mode"        => $modo
        ]);

        $this->path = PATH_OS ."{$template}".'.pdf';

        if($acao == 'abrirOs') {

            return $this->openPdf();
            
        } else {
            $this->pdf->WriteHTML("{$template}");
    
            $this->pdf->Output($this->path);
        }
    }

    public function openPdf()
    {
        
        return popen($this->path, 'r');
        // $this->closePdf();
    }

}

?>