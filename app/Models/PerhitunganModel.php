<?php

namespace App\Models;

use CodeIgniter\Model;

class PerhitunganModel extends Model
{
    protected $table = 'hasil';
    protected $primaryKey = 'id_hasil';
    protected $allowedFields = ['id_alternatif', 'id_kriteria', 'nilai'];

    public function getKriteria()
    {
        return $this->db->table('kriteria')->get()->getResult();
    }

    public function getAlternatif()
    {
        return $this->db->table('alternatif')->get()->getResult();
    }

    public function getDeskripsi()
    {
        return $this->db->table('sub_kriteria')->get()->getResult();
    }

    public function dataNilai($id_alternatif, $id_kriteria)
    {
        return $this->db->table('penilaian')
                        ->join('sub_kriteria', 'penilaian.nilai = sub_kriteria.id_sub_kriteria')
                        ->where(['penilaian.id_alternatif' => $id_alternatif, 'penilaian.id_kriteria' => $id_kriteria])
                        ->get()
                        ->getRowArray();
    }

    public function getMaxMin($id_kriteria)
    {
        return $this->db->table('penilaian')
                        ->join('sub_kriteria', 'penilaian.nilai = sub_kriteria.id_sub_kriteria')
                        ->join('kriteria', 'penilaian.id_kriteria = kriteria.id_kriteria')
                        ->select('max(sub_kriteria.nilai) as max, min(sub_kriteria.nilai) as min, sub_kriteria.nilai as nilai, kriteria.jenis')
                        ->where('penilaian.id_kriteria', $id_kriteria)
                        ->get()
                        ->getRowArray();
    }

    public function getHasil()
    {
        return $this->orderBy('nilai', 'DESC')->findAll(15);
    }

    public function getHasilBanyak()
    {
        return $this->orderBy('nilai', 'DESC')->findAll();
    }

    public function getHasilAlternatif($id_alternatif)
    {
        return $this->db->table('alternatif')->where('id_alternatif', $id_alternatif)->get()->getRowArray();
    }

    public function insertNilaiHasil($hasil_akhir)
    {
        return $this->insert($hasil_akhir);
    }

    public function hapusHasil()
    {
        return $this->truncate();
    }
}