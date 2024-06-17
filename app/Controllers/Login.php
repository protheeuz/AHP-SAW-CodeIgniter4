<?php

namespace App\Controllers;

use App\Models\LoginModel;
use CodeIgniter\Controller;

class Login extends Controller
{
    protected $loginModel;
    protected $session;

    public function __construct()
    {
        helper(['url', 'form']);
        $this->session = \Config\Services::session();
        $this->loginModel = new LoginModel();
    }

    public function index()
    {
        if ($this->loginModel->loggedId()) {
            return redirect()->to('Login/home');
        } else {
            return view('login');
        }
    }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        if (is_string($username) && is_string($password)) {
            $user = $this->loginModel->login($username, $password);

            if ($user) {
                $log = [
                    'id_user' => $user['id_user'],
                    'username' => $user['username'],
                    'id_user_level' => $user['id_user_level'],
                    'status' => 'Logged'
                ];
                $this->session->set($log);
                return redirect()->to('Login/home');
            } else {
                $this->session->setFlashdata('message', 'Username atau Password Salah');
                return redirect()->to('login');
            }
        } else {
            $this->session->setFlashdata('message', 'Invalid input data');
            return redirect()->to('login');
        }
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('login');
    }

    public function home()
    {
        $data['page'] = "Dashboard";
        return view('admin/index', $data);
    }
}