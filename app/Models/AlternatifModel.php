<?php

namespace App\Models;

use CodeIgniter\Model;

class AlternatifModel extends Model
{
    protected $table = 'alternatif';
    protected $primaryKey = 'id_alternatif';
    protected $allowedFields = ['nama'];

    public function tampil()
    {
        return $this->findAll();
    }

    public function getTotal()
    {
        return $this->countAll();
    }

    public function insertAlternatif($data)
    {
        return $this->insert($data);
    }

    public function show($id_alternatif)
    {
        return $this->find($id_alternatif);
    }

    public function updateAlternatif($id_alternatif, $data)
    {
        return $this->update($id_alternatif, $data);
    }

    public function deleteAlternatif($id_alternatif)
    {
        return $this->delete($id_alternatif);
    }
}
