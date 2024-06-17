<?php

namespace App\Controllers;

use App\Models\PenilaianModel;
use CodeIgniter\Controller;

class Penilaian extends Controller
{
    protected $pagination;
    protected $form_validation;
    protected $penilaianModel;
    protected $session;

    public function __construct()
    {
        helper('url');
        $this->pagination = \Config\Services::pagination();
        $this->form_validation = \Config\Services::validation();
        $this->penilaianModel = new PenilaianModel();
        $this->session = \Config\Services::session();

        if ($this->session->get('id_user_level') != "1") {
            echo '<script type="text/javascript">
                    alert("Anda tidak berhak mengakses halaman ini!");
                    window.location="' . base_url("Login/home") . '";
                </script>';
        }
    }

    public function index()
    {
        $data = [
            'page' => "Penilaian",
            'list' => $this->penilaianModel->tampil(),
            'kriteria' => $this->penilaianModel->getKriteria(),
            'alternatif' => $this->penilaianModel->getAlternatif(),
            'sub_kriteria' => $this->penilaianModel->getSubKriteria(),
            'perhitungan' => $this->penilaianModel->tampil()
        ];
        return view('penilaian/index', $data);
    }

    public function tambah_penilaian()
    {
        $id_alternatif = $this->request->getPost('id_alternatif');
        $id_kriteria = $this->request->getPost('id_kriteria');
        $nilai = $this->request->getPost('nilai');
        $i = 0;
        foreach ($nilai as $key) {
            $this->penilaianModel->tambahPenilaian([
                'id_alternatif' => $id_alternatif,
                'id_kriteria' => $id_kriteria[$i],
                'nilai' => $key
            ]);
            $i++;
        }
        $this->session->setFlashdata('message', '<div class="alert alert-success" role="alert">Data berhasil disimpan!</div>');
        return redirect()->to('penilaian');
    }

    public function update_penilaian()
    {
        $id_alternatif = $this->request->getPost('id_alternatif');
        $id_kriteria = $this->request->getPost('id_kriteria');
        $nilai = $this->request->getPost('nilai');
        $i = 0;

        foreach ($nilai as $key) {
            $cek = $this->penilaianModel->dataPenilaian($id_alternatif, $id_kriteria[$i]);
            if ($cek == 0) {
                $this->penilaianModel->tambahPenilaian([
                    'id_alternatif' => $id_alternatif,
                    'id_kriteria' => $id_kriteria[$i],
                    'nilai' => $key
                ]);
            } else {
                $this->penilaianModel->editPenilaian($id_alternatif, $id_kriteria[$i], $key);
            }
            $i++;
        }
        $this->session->setFlashdata('message', '<div class="alert alert-success" role="alert">Data berhasil diupdate!</div>');
        return redirect()->to('penilaian');
    }
}