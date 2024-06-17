<?php

namespace App\Models;

use CodeIgniter\Model;

class PenilaianModel extends Model
{
    protected $table = 'penilaian';
    protected $primaryKey = 'id_penilaian';
    protected $allowedFields = ['id_alternatif', 'id_kriteria', 'nilai'];

    public function tampil()
    {
        return $this->findAll();
    }

    public function tambahPenilaian($data)
    {
        return $this->insert($data);
    }

    public function editPenilaian($id_alternatif, $id_kriteria, $nilai)
    {
        return $this->where(['id_alternatif' => $id_alternatif, 'id_kriteria' => $id_kriteria])
                    ->set('nilai', $nilai)
                    ->update();
    }

    public function deletePenilaian($id_penilaian)
    {
        return $this->delete($id_penilaian);
    }

    public function getKriteria()
    {
        return $this->db->table('kriteria')->get()->getResult();
    }

    public function getAlternatif()
    {
        return $this->db->table('alternatif')->get()->getResult();
    }

    public function getSubKriteria()
    {
        return $this->db->table('sub_kriteria')->get()->getResult();
    }

    public function dataPenilaian($id_alternatif, $id_kriteria)
    {
        return $this->where(['id_alternatif' => $id_alternatif, 'id_kriteria' => $id_kriteria])->first();
    }

    public function untukTombol($id_alternatif)
    {
        return $this->where('id_alternatif', $id_alternatif)->countAllResults();
    }

    public function dataSubKriteria($id_kriteria)
    {
        return $this->db->table('sub_kriteria')->where('id_kriteria', $id_kriteria)->orderBy('nilai', 'DESC')->get()->getResultArray();
    }

    public function dataNilai($id_alternatif, $id_kriteria)
    {
        return $this->db->table('penilaian')
                        ->join('sub_kriteria', 'penilaian.nilai = sub_kriteria.id_sub_kriteria')
                        ->where(['penilaian.id_alternatif' => $id_alternatif, 'penilaian.id_kriteria' => $id_kriteria])
                        ->get()
                        ->getRowArray();
    }
}