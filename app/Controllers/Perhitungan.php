<?php

namespace App\Controllers;

use App\Models\PerhitunganModel;
use CodeIgniter\Controller;

class Perhitungan extends Controller
{
    protected $pagination;
    protected $form_validation;
    protected $perhitunganModel;
    protected $session;

    public function __construct()
    {
        helper('url');
        $this->pagination = \Config\Services::pagination();
        $this->form_validation = \Config\Services::validation();
        $this->perhitunganModel = new PerhitunganModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        if ($this->session->get('id_user_level') != "1") {
            echo '<script type="text/javascript">
                    alert("Anda tidak berhak mengakses halaman ini!");
                    window.location="' . base_url("Login/home") . '";
                </script>';
        }

        $data = [
            'page' => "Perhitungan",
            'kriteria' => $this->perhitunganModel->getKriteria(),
            'alternatif' => $this->perhitunganModel->getAlternatif(),
            'deskripsi' => $this->perhitunganModel->getDeskripsi(),
        ];

        return view('perhitungan/perhitungan', $data);
    }

    public function hasil()
    {
        $data = [
            'page' => "Hasil",
            'hasil' => $this->perhitunganModel->getHasilBanyak()
        ];

        return view('perhitungan/hasil', $data);
    }
}