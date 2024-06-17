<?php

namespace App\Controllers;

use App\Models\AlternatifModel;
use CodeIgniter\Controller;

class Alternatif extends Controller
{
    protected $pagination;
    protected $form_validation;
    protected $alternatifModel;
    protected $session;

    public function __construct()
    {
        helper('url');
        $this->pagination = \Config\Services::pagination();
        $this->form_validation = \Config\Services::validation();
        $this->alternatifModel = new AlternatifModel();
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
            'page' => "Alternatif",
            'list' => $this->alternatifModel->findAll()
        ];
        return view('alternatif/index', $data);
    }

    public function create()
    {
        $data['page'] = "Alternatif";
        return view('alternatif/create', $data);
    }

    public function store()
    {
        $data = [
            'nama' => $this->request->getPost('nama')
        ];

        $this->form_validation->setRules([
            'nama' => 'required'
        ]);

        if ($this->form_validation->withRequest($this->request)->run()) {
            $result = $this->alternatifModel->insert($data);
            if ($result) {
                $this->session->setFlashdata('message', '<div class="alert alert-success" role="alert">Data berhasil disimpan!</div>');
                return redirect()->to('alternatif');
            }
        } else {
            $this->session->setFlashdata('message', '<div class="alert alert-danger" role="alert">Data gagal disimpan!</div>');
            return redirect()->to('alternatif/create');
        }
    }

    public function edit($id_alternatif)
    {
        $alternatif = $this->alternatifModel->find($id_alternatif);
        $data = [
            'page' => "Alternatif",
            'alternatif' => $alternatif
        ];
        return view('alternatif/edit', $data);
    }

    public function update($id_alternatif)
    {
        $data = [
            'nama' => $this->request->getPost('nama')
        ];

        $this->alternatifModel->update($id_alternatif, $data);
        $this->session->setFlashdata('message', '<div class="alert alert-success" role="alert">Data berhasil diupdate!</div>');
        return redirect()->to('alternatif');
    }

    public function destroy($id_alternatif)
    {
        $this->alternatifModel->delete($id_alternatif);
        $this->session->setFlashdata('message', '<div class="alert alert-success" role="alert">Data berhasil dihapus!</div>');
        return redirect()->to('alternatif');
    }
}