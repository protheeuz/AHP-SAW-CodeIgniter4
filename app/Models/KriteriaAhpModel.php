<?php

namespace App\Models;

use CodeIgniter\Model;

class KriteriaAhpModel extends Model
{
    protected $table = 'kriteria_ahp';
    protected $allowedFields = ['id_kriteria_1', 'id_kriteria_2', 'nilai_1', 'nilai_2'];

    public function getKriteriaAhp($id_kriteria_1, $id_kriteria_2)
    {
        return $this->where(['id_kriteria_1' => $id_kriteria_1, 'id_kriteria_2' => $id_kriteria_2])->first();
    }

    public function addKriteriaAhp($params)
    {
        return $this->insert($params);
    }

    public function updateKriteriaAhp($id_kriteria_1, $id_kriteria_2, $params)
    {
        return $this->where(['id_kriteria_1' => $id_kriteria_1, 'id_kriteria_2' => $id_kriteria_2])->set($params)->update();
    }

    public function deleteKriteriaAhp()
    {
        return $this->truncate();
    }
}