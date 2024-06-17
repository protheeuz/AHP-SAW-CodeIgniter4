<?php

namespace App\Controllers;

use App\Models\PerhitunganModel;
use CodeIgniter\Controller;

class Laporan extends Controller
{
    protected $perhitunganModel;

    public function __construct()
    {
        $this->perhitunganModel = new PerhitunganModel();
    }

    public function cetak_laporan_hasil()
    {
        $data = [
            'hasil' => $this->perhitunganModel->getHasil()
        ];

        $this->load->library('pdf');
        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->filename = "Laporan_Hasil.pdf";
        $this->pdf->load_view('laporan_hasil', $data);
    }
}