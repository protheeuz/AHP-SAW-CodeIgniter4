<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfileModel extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'id_user';
    protected $allowedFields = ['email', 'nama', 'username', 'password'];

    public function show($id_user)
    {
        return $this->find($id_user);
    }

    public function updateProfile($id_user, $data)
    {
        return $this->update($id_user, $data);
    }
}