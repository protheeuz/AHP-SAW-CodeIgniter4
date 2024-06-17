<?php

namespace App\Models;

use CodeIgniter\Model;

class KriteriaModel extends Model
{
    protected $table = 'kriteria';
    protected $primaryKey = 'id_kriteria';
    protected $allowedFields = ['keterangan', 'kode_kriteria', 'jenis'];

    public function tampil()
    {
        return $this->findAll();
    }

    public function getAllKriteria($sort = 'asc')
    {
        return $this->orderBy('id_kriteria', $sort)->findAll();
    }

    public function getKriteria($id_kriteria)
    {
        return $this->find($id_kriteria);
    }

    public function updateKriteria($id_kriteria, $params)
    {
        return $this->update($id_kriteria, $params);
    }

    public function updatePrioritas($params)
    {
        return $this->update($params);
    }

    public function insertKriteria($data)
    {
        return $this->insert($data);
    }

    public function show($id_kriteria)
    {
        return $this->find($id_kriteria);
    }

    public function deleteKriteria($id_kriteria)
    {
        return $this->delete($id_kriteria);
    }

    public function totalKriteria()
    {
        return $this->countAll();
    }
}