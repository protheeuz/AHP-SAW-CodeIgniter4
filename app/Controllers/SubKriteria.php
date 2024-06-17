<?php

namespace App\Controllers;

use App\Models\SubKriteriaModel;
use CodeIgniter\Controller;

class SubKriteria extends Controller
{
    protected $pagination;
    protected $form_validation;
    protected $subKriteriaModel;
    protected $session;

    public function __construct()
    {
        helper('url');
        $this->pagination = \Config\Services::pagination();
        $this->form_validation = \Config\Services::validation();
        $this->subKriteriaModel = new SubKriteriaModel();
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
            'page' => "Sub Kriteria",
            'list' => $this->subKriteriaModel->tampil(),
            'kriteria' => $this->subKriteriaModel->getKriteria(),
            'count_kriteria' => $this->subKriteriaModel->countKriteria(),
            'sub_kriteria' => $this->subKriteriaModel->tampil()
        ];
        return view('sub_kriteria/index', $data);
    }

    public function store()
    {
        $data = [
            'id_kriteria' => $this->request->getPost('id_kriteria'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'nilai' => $this->request->getPost('nilai')
        ];

        $this->form_validation->setRules([
            'id_kriteria' => 'required',
            'deskripsi' => 'required',
            'nilai' => 'required'
        ]);

        if ($this->form_validation->withRequest($this->request)->run()) {
            $result = $this->subKriteriaModel->insert($data);
            if ($result) {
                $this->session->setFlashdata('message', '<div class="alert alert-success" role="alert">Data berhasil disimpan!</div>');
                return redirect()->to('sub_kriteria');
            }
        } else {
            $this->session->setFlashdata('message', '<div class="alert alert-danger" role="alert">Data gagal disimpan!</div>');
            return redirect()->to('sub_kriteria/create');
        }
    }

    public function update($id_sub_kriteria)
    {
        $data = [
            'id_kriteria' => $this->request->getPost('id_kriteria'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'nilai' => $this->request->getPost('nilai')
        ];

        $this->subKriteriaModel->update($id_sub_kriteria, $data);
        $this->session->setFlashdata('message', '<div class="alert alert-success" role="alert">Data berhasil diupdate!</div>');
        return redirect()->to('sub_kriteria');
    }

    public function destroy($id_sub_kriteria)
    {
        $this->subKriteriaModel->delete($id_sub_kriteria);
        $this->session->setFlashdata('message', '<div class="alert alert-success" role="alert">Data berhasil dihapus!</div>');
        return redirect()->to('sub_kriteria');
    }
}