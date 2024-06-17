<?php

namespace App\Controllers;

use App\Models\ProfileModel;
use CodeIgniter\Controller;

class Profile extends Controller
{
    protected $pagination;
    protected $form_validation;
    protected $profileModel;
    protected $session;

    public function __construct()
    {
        helper('url');
        $this->pagination = \Config\Services::pagination();
        $this->form_validation = \Config\Services::validation();
        $this->profileModel = new ProfileModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $id_user = $this->session->get('id_user');
        $profile = $this->profileModel->find($id_user);
        $data = [
            'page' => "Profile",
            'profile' => $profile
        ];
        return view('profile/index', $data);
    }

    public function update($id_user)
    {
        $nama = $this->request->getPost('nama');
        $email = $this->request->getPost('email');
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Periksa apakah input adalah string
        if (is_string($nama) && is_string($email) && is_string($username) && is_string($password)) {
            $data = [
                'nama' => $nama,
                'email' => $email,
                'username' => $username,
                'password' => md5($password)
            ];

            $this->profileModel->update($id_user, $data);
            $this->session->setFlashdata('message', '<div class="alert alert-success" role="alert">Data berhasil diupdate!</div>');
            return redirect()->to('profile');
        } else {
            $this->session->setFlashdata('message', '<div class="alert alert-danger" role="alert">Invalid input data</div>');
            return redirect()->to('profile');
        }
    }
}