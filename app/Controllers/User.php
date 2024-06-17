<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class User extends Controller
{
    protected $pagination;
    protected $form_validation;
    protected $userModel;
    protected $session;

    public function __construct()
    {
        helper('url');
        $this->pagination = \Config\Services::pagination();
        $this->form_validation = \Config\Services::validation();
        $this->userModel = new UserModel();
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
            'page' => "User",
            'list' => $this->userModel->tampil(),
            'user_level' => $this->userModel->userLevel()
        ];
        return view('user/index', $data);
    }

    public function create()
    {
        $data['page'] = "User";
        $data['user_level'] = $this->userModel->userLevel();
        return view('user/create', $data);
    }

    public function store()
    {
        $id_user_level = $this->request->getPost('privilege');
        $nama = $this->request->getPost('nama');
        $email = $this->request->getPost('email');
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Periksa apakah input adalah string
        if (is_string($id_user_level) && is_string($nama) && is_string($email) && is_string($username) && is_string($password)) {
            $data = [
                'id_user_level' => $id_user_level,
                'nama' => $nama,
                'email' => $email,
                'username' => $username,
                'password' => md5($password)
            ];

            $this->form_validation->setRules([
                'email' => 'required',
                'privilege' => 'required',
                'username' => 'required|is_unique[user.username]',
                'password' => 'required'
            ]);

            if ($this->form_validation->withRequest($this->request)->run()) {
                $result = $this->userModel->insert($data);
                if ($result) {
                    $this->session->setFlashdata('message', '<div class="alert alert-success" role="alert">Data berhasil disimpan!</div>');
                    return redirect()->to('user');
                }
            } else {
                $this->session->setFlashdata('message', '<div class="alert alert-danger" role="alert">Data gagal disimpan!</div>');
                return redirect()->to('user/create');
            }
        } else {
            $this->session->setFlashdata('message', '<div class="alert alert-danger" role="alert">Invalid input data</div>');
            return redirect()->to('user/create');
        }
    }

    public function show($id_user)
    {
        $user = $this->userModel->find($id_user);
        $user_level = $this->userModel->userLevel();
        $data = [
            'page' => "User",
            'data' => $user,
            'user_level' => $user_level
        ];
        return view('user/show', $data);
    }

    public function edit($id_user)
    {
        $user = $this->userModel->find($id_user);
        $user_level = $this->userModel->userLevel();
        $data = [
            'page' => "User",
            'User' => $user,
            'user_level' => $user_level
        ];
        return view('user/edit', $data);
    }

    public function update($id_user)
    {
        $id_user_level = $this->request->getPost('privilege');
        $nama = $this->request->getPost('nama');
        $email = $this->request->getPost('email');
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Periksa apakah input adalah string
        if (is_string($id_user_level) && is_string($nama) && is_string($email) && is_string($username) && is_string($password)) {
            $data = [
                'id_user_level' => $id_user_level,
                'nama' => $nama,
                'email' => $email,
                'username' => $username,
                'password' => md5($password)
            ];

            $this->userModel->update($id_user, $data);
            $this->session->setFlashdata('message', '<div class="alert alert-success" role="alert">Data berhasil diupdate!</div>');
            return redirect()->to('user');
        } else {
            $this->session->setFlashdata('message', '<div class="alert alert-danger" role="alert">Invalid input data</div>');
            return redirect()->to('user/edit/'.$id_user);
        }
    }

    public function destroy($id_user)
    {
        $this->userModel->delete($id_user);
        $this->session->setFlashdata('message', '<div class="alert alert-success" role="alert">Data berhasil dihapus!</div>');
        return redirect()->to('user');
    }
}