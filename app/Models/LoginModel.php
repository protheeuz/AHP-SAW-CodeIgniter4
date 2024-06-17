<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginModel extends Model
{
    protected $table = 'users'; // Sesuaikan nama tabel
    protected $primaryKey = 'id_user';
    protected $allowedFields = ['username', 'password'];

    public function loggedId()
    {
        return session()->get('id_user');
    }

    public function login($username, $password)
    {
        // Hash password sebelum mencari di database
        $passwordHash = md5($password); // atau gunakan password_hash dan password_verify
        return $this->where(['username' => $username, 'password' => $passwordHash])->first();
    }
}