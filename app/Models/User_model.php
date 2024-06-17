<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'id_user';
    protected $allowedFields = ['id_user_level', 'email', 'nama', 'username', 'password'];

    public function tampil()
    {
        return $this->findAll();
    }

    public function getTotal()
    {
        return $this->countAll();
    }

    public function insertUser($data)
    {
        return $this->insert($data);
    }

    public function show($id_user)
    {
        return $this->find($id_user);
    }

    public function updateUser($id_user, $data)
    {
        return $this->update($id_user, $data);
    }

    public function deleteUser($id_user)
    {
        return $this->delete($id_user);
    }

    public function getUser()
    {
        return $this->findAll();
    }

    public function userLevel()
    {
        return $this->db->table('user_level')->get()->getResult();
    }
}