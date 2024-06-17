<?php

namespace App\Models;

use CodeIgniter\Model;

class SubKriteriaModel extends Model
{
    protected $table = 'sub_kriteria';
    protected $primaryKey = 'id_sub_kriteria';
    protected $allowedFields = ['id_kriteria', 'deskripsi', 'nilai'];

    public function tampil()
    {
        return $this->findAll();
    }

    public function getTotal()
    {
        return $this->countAll();
    }

    public function insertSubKriteria($data)
    {
        return $this->insert($data);
    }

    public function show($id_sub_kriteria)
    {
        return $this->find($id_sub_kriteria);
    }

    public function updateSubKriteria($id_sub_kriteria, $data)
    {
        return $this->update($id_sub_kriteria, $data);
    }

    public function deleteSubKriteria($id_sub_kriteria)
    {
        return $this->delete($id_sub_kriteria);
    }

    public function getKriteria()
    {
        return $this->db->table('kriteria')->get()->getResult();
    }

    public function countKriteria()
    {
        return $this->db->query("SELECT id_kriteria, COUNT(deskripsi) AS jml_setoran FROM sub_kriteria GROUP BY id_kriteria")->getResult();
    }

    public function dataSubKriteria($id_kriteria)
    {
        return $this->db->table('sub_kriteria')->where('id_kriteria', $id_kriteria)->orderBy('nilai', 'DESC')->get()->getResultArray();
    }
}