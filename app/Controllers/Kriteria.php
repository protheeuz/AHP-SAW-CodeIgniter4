<?php

namespace App\Controllers;

use App\Models\KriteriaModel;
use App\Models\KriteriaAhpModel;
use CodeIgniter\Controller;

class Kriteria extends Controller
{
    protected $pagination;
    protected $form_validation;
    protected $kriteriaModel;
    protected $kriteriaAhpModel;
    protected $session;

    public function __construct()
    {
        helper('url');
        $this->pagination = \Config\Services::pagination();
        $this->form_validation = \Config\Services::validation();
        $this->kriteriaModel = new KriteriaModel();
        $this->kriteriaAhpModel = new KriteriaAhpModel();
        $this->session = \Config\Services::session();

        if ($this->session->get('id_user_level') != "1") {
            echo '<script type="text/javascript">
                    alert("Anda tidak berhak mengakses halaman ini!");
                    window.location="' . base_url("Login/home") . '";
                </script>';
        }
    }

    public function index()
    {
        $data = [
            'page' => "Kriteria",
            'list' => $this->kriteriaModel->tampil()
        ];
        return view('kriteria/index', $data);
    }

    public function total_kriteria()
    {
        $mydata['total_kriteria'] = $this->kriteriaModel->totalKriteria();
        return view('admin/index', $mydata);
    }

    public function create()
    {
        $data['page'] = "Kriteria";
        return view('kriteria/create', $data);
    }

    public function store()
    {
        $data = [
            'keterangan' => $this->request->getPost('keterangan'),
            'kode_kriteria' => $this->request->getPost('kode_kriteria'),
            'jenis' => $this->request->getPost('jenis')
        ];

        $this->form_validation->setRules([
            'keterangan' => 'required',
            'kode_kriteria' => 'required',
            'jenis' => 'required'
        ]);

        if ($this->form_validation->withRequest($this->request)->run()) {
            $result = $this->kriteriaModel->insert($data);
            if ($result) {
                $this->session->setFlashdata('message', '<div class="alert alert-success" role="alert">Data berhasil disimpan!</div>');
                return redirect()->to('kriteria');
            }
        } else {
            $this->session->setFlashdata('message', '<div class="alert alert-danger" role="alert">Data gagal disimpan!</div>');
            return redirect()->to('kriteria/create');
        }
    }

    public function edit($id_kriteria)
    {
        $data['page'] = "Kriteria";
        $data['kriteria'] = $this->kriteriaModel->find($id_kriteria);
        return view('kriteria/edit', $data);
    }

    public function update($id_kriteria)
    {
        $data = [
            'keterangan' => $this->request->getPost('keterangan'),
            'kode_kriteria' => $this->request->getPost('kode_kriteria'),
            'jenis' => $this->request->getPost('jenis')
        ];

        $this->kriteriaModel->update($id_kriteria, $data);
        $this->session->setFlashdata('message', '<div class="alert alert-success" role="alert">Data berhasil diupdate!</div>');
        return redirect()->to('kriteria');
    }

    public function destroy($id_kriteria)
    {
        $this->kriteriaModel->delete($id_kriteria);
        $this->session->setFlashdata('message', '<div class="alert alert-success" role="alert">Data berhasil dihapus!</div>');
        return redirect()->to('kriteria');
    }

    public function prioritas()
    {
        $data['page'] = "Kriteria";
        $query_kriteria = $this->kriteriaModel->findAll();
        $data['kriteria'] = $query_kriteria;

        if ($this->request->getPost('save')) {
            $this->kriteriaAhpModel->deleteKriteriaAhp();
            $i = 0;
            foreach ($data['kriteria'] as $row1) {
                $ii = 0;
                foreach ($data['kriteria'] as $row2) {
                    if ($i < $ii) {
                        $nilai_input = $this->request->getPost('nilai_' . $row1['id_kriteria'] . '_' . $row2['id_kriteria']);

                        if (is_numeric($nilai_input)) {
                            $nilai_1 = 0;
                            $nilai_2 = 0;
                            if ($nilai_input < 1) {
                                $nilai_1 = abs($nilai_input);
                                $nilai_2 = number_format(1 / abs($nilai_input), 5);
                            } elseif ($nilai_input > 1) {
                                $nilai_1 = number_format(1 / abs($nilai_input), 5);
                                $nilai_2 = abs($nilai_input);
                            } elseif ($nilai_input == 1) {
                                $nilai_1 = 1;
                                $nilai_2 = 1;
                            }
                            $params = [
                                'id_kriteria_1' => $row1['id_kriteria'],
                                'id_kriteria_2' => $row2['id_kriteria'],
                                'nilai_1' => $nilai_1,
                                'nilai_2' => $nilai_2,
                            ];
                            $this->kriteriaAhpModel->insert($params);
                        }
                    }
                    $ii++;
                }
                $i++;
            }
            $this->session->setFlashdata('message', '<div class="alert alert-success" role="alert">Nilai perbandingan kriteria berhasil disimpan!</div>');
        }

        if ($this->request->getPost('check')) {
            if (count($query_kriteria) < 3) {
                $this->session->setFlashdata('pesan_error', '<div class="alert alert-danger" role="alert">Jumlah kriteria kurang, minimal 3!</div>');
            } else {
                $id_kriteria = array_column($data['kriteria'], 'id_kriteria');

                // Perhitungan metode AHP
                $matrik_kriteria = $this->ahp_get_matrik_kriteria($id_kriteria);
                $jumlah_kolom = $this->ahp_get_jumlah_kolom($matrik_kriteria);
                $matrik_normalisasi = $this->ahp_get_normalisasi($matrik_kriteria, $jumlah_kolom);
                $prioritas = $this->ahp_get_prioritas($matrik_normalisasi);
                $matrik_baris = $this->ahp_get_matrik_baris($prioritas, $matrik_kriteria);
                $jumlah_matrik_baris = $this->ahp_get_jumlah_matrik_baris($matrik_baris);
                $hasil_tabel_konsistensi = $this->ahp_get_tabel_konsistensi($jumlah_matrik_baris, $prioritas);
                if ($this->ahp_uji_konsistensi($hasil_tabel_konsistensi)) {
                    $this->session->setFlashdata('message', '<div class="alert alert-success" role="alert">Nilai perbandingan : KONSISTEN!</div>');
                    $i = 0;
                    foreach ($data['kriteria'] as $row) {
                        $params = [
                            'bobot' => $prioritas[$i++],
                        ];
                        $this->kriteriaModel->update($row['id_kriteria'], $params);
                    }

                    $data['list_data'] = $this->tampil_data_1($matrik_kriteria, $jumlah_kolom);
                    $data['list_data2'] = $this->tampil_data_2($matrik_normalisasi, $prioritas);
                    $data['list_data3'] = $this->tampil_data_3($matrik_baris, $jumlah_matrik_baris);
                    $list_data = $this->tampil_data_4($jumlah_matrik_baris, $prioritas, $hasil_tabel_konsistensi);
                    $data['list_data4'] = $list_data[0];
                    $data['list_data5'] = $list_data[1];
                } else {
                    $this->session->setFlashdata('pesan_error', '<div class="alert alert-danger" role="alert">Nilai perbandingan : TIDAK KONSISTEN</div>');
                }
            }
        }

        $result = [];
        $i = 0;
        foreach ($data['kriteria'] as $row1) {
            $ii = 0;
            foreach ($data['kriteria'] as $row2) {
                if ($i < $ii) {
                    $kriteria_ahp = $this->kriteriaAhpModel->getKriteriaAhp($row1['id_kriteria'], $row2['id_kriteria']);
                    if (empty($kriteria_ahp)) {
                        $params = [
                            'id_kriteria_1' => $row1['id_kriteria'],
                            'id_kriteria_2' => $row2['id_kriteria'],
                            'nilai_1' => 1,
                            'nilai_2' => 1,
                        ];
                        $this->kriteriaAhpModel->insert($params);
                        $nilai_1 = 1;
                        $nilai_2 = 1;
                    } else {
                        $nilai_1 = $kriteria_ahp['nilai_1'];
                        $nilai_2 = $kriteria_ahp['nilai_2'];
                    }
                    $nilai = 0;
                    if ($nilai_1 < 1) {
                        $nilai = $nilai_2;
                    } elseif ($nilai_1 > 1) {
                        $nilai = -$nilai_1;
                    } elseif ($nilai_1 == 1) {
                        $nilai = 1;
                    }
                    $result[$row1['id_kriteria']][$row2['id_kriteria']] = $nilai;
                }
                $ii++;
            }
            $i++;
        }

        $data['kriteria_ahp'] = $result;
        return view('kriteria/prioritas', $data);
    }

    public function reset()
    {
        $this->kriteriaAhpModel->deleteKriteriaAhp();
        $params = [
            'bobot' => null,
        ];
        $this->kriteriaModel->updatePrioritas($params);
        $this->session->setFlashdata('message', '<div class="alert alert-success" role="alert">Data berhasil direset!</div>');
        return redirect()->to('kriteria/prioritas');
    }

    // --- metode AHP --- START
    public function ahp_get_matrik_kriteria($kriteria)
    {
        $matrik = [];
        $i = 0;
        foreach ($kriteria as $row1) {
            $ii = 0;
            foreach ($kriteria as $row2) {
                if ($i == $ii) {
                    $matrik[$i][$ii] = 1;
                } else {
                    if ($i < $ii) {
                        $kriteria_ahp = $this->kriteriaAhpModel->getKriteriaAhp($row1, $row2);
                        if (empty($kriteria_ahp)) {
                            $matrik[$i][$ii] = 1;
                            $matrik[$ii][$i] = 1;
                        } else {
                            $matrik[$i][$ii] = $kriteria_ahp['nilai_1'];
                            $matrik[$ii][$i] = $kriteria_ahp['nilai_2'];
                        }
                    }
                }
                $ii++;
            }
            $i++;
        }
        return $matrik;
    }

    public function ahp_get_jumlah_kolom($matrik)
    {
        $jumlah_kolom = [];
        for ($i = 0; $i < count($matrik); $i++) {
            $jumlah_kolom[$i] = 0;
            for ($ii = 0; $ii < count($matrik); $ii++) {
                $jumlah_kolom[$i] += $matrik[$ii][$i];
            }
        }
        return $jumlah_kolom;
    }

    public function ahp_get_normalisasi($matrik, $jumlah_kolom)
    {
        $matrik_normalisasi = [];
        for ($i = 0; $i < count($matrik); $i++) {
            for ($ii = 0; $ii < count($matrik); $ii++) {
                $matrik_normalisasi[$i][$ii] = number_format($matrik[$i][$ii] / $jumlah_kolom[$ii], 5);
            }
        }
        return $matrik_normalisasi;
    }

    public function ahp_get_prioritas($matrik_normalisasi)
    {
        $prioritas = [];
        for ($i = 0; $i < count($matrik_normalisasi); $i++) {
            $prioritas[$i] = 0;
            for ($ii = 0; $ii < count($matrik_normalisasi); $ii++) {
                $prioritas[$i] += $matrik_normalisasi[$i][$ii];
            }
            $prioritas[$i] = number_format($prioritas[$i] / count($matrik_normalisasi), 5);
        }
        return $prioritas;
    }

    public function ahp_get_matrik_baris($prioritas, $matrik_kriteria)
    {
        $matrik_baris = [];
        for ($i = 0; $i < count($matrik_kriteria); $i++) {
            for ($ii = 0; $ii < count($matrik_kriteria); $ii++) {
                $matrik_baris[$i][$ii] = number_format($prioritas[$ii] * $matrik_kriteria[$i][$ii], 5);
            }
        }
        return $matrik_baris;
    }

    public function ahp_get_jumlah_matrik_baris($matrik_baris)
    {
        $jumlah_baris = [];
        for ($i = 0; $i < count($matrik_baris); $i++) {
            $jumlah_baris[$i] = 0;
            for ($ii = 0; $ii < count($matrik_baris); $ii++) {
                $jumlah_baris[$i] += $matrik_baris[$i][$ii];
            }
        }
        return $jumlah_baris;
    }

    public function ahp_get_tabel_konsistensi($jumlah_matrik_baris, $prioritas)
    {
        $jumlah = [];
        for ($i = 0; $i < count($jumlah_matrik_baris); $i++) {
            $jumlah[$i] = $jumlah_matrik_baris[$i] + $prioritas[$i];
        }
        return $jumlah;
    }

    public function ahp_uji_konsistensi($tabel_konsistensi)
    {
        $jumlah = array_sum($tabel_konsistensi);
        $n = count($tabel_konsistensi);
        $lambda_maks = $jumlah / $n;
        $ci = ($lambda_maks - $n) / ($n - 1);
        $ir = [0, 0, 0.58, 0.9, 1.12, 1.24, 1.32, 1.41, 1.45, 1.49, 1.51, 1.48, 1.56, 1.57, 1.59];
        if ($n <= 15) {
            $ir = $ir[$n - 1];
        } else {
            $ir = $ir[14];
        }
        $cr = number_format($ci / $ir, 5);

        return $cr <= 0.1;
    }
    // --- metode AHP --- END

    // --- untuk menampilkan langkah perhitungan ---
    public function tampil_data_1($matrik_kriteria, $jumlah_kolom)
    {
        $kriteria = $this->kriteriaModel->findAll();
        // --- tabel matriks perbandingan berpasangan
        $list_data = '';
        $list_data .= '<tr><td></td>';
        foreach ($kriteria as $row) {
            $list_data .= '<td class="text-center">' . $row['kode_kriteria'] . '</td>';
        }
        $list_data .= '</tr>';
        $i = 0;
        foreach ($kriteria as $row) {
            $list_data .= '<tr>';
            $list_data .= '<td>' . $row['kode_kriteria'] . '</td>';
            $ii = 0;
            foreach ($kriteria as $row2) {
                $list_data .= '<td class="text-center">' . $matrik_kriteria[$i][$ii] . '</td>';
                $ii++;
            }
            $list_data .= '</tr>';
            $i++;
        }
        $list_data .= '<tr><td class="font-weight-bold">Jumlah</td>';
        for ($i = 0; $i < count($jumlah_kolom); $i++) {
            $list_data .= '<td class="text-center font-weight-bold">' . $jumlah_kolom[$i] . '</td>';
        }
        $list_data .= '</tr>';
        // ---
        return $list_data;
    }

    public function tampil_data_2($matrik_normalisasi, $prioritas)
    {
        $kriteria = $this->kriteriaModel->findAll();
        // --- matriks nilai kriteria
        $list_data2 = '';
        $list_data2 .= '<tr><td></td>';
        foreach ($kriteria as $row) {
            $list_data2 .= '<td class="text-center">' . $row['kode_kriteria'] . '</td>';
        }
        $list_data2 .= '<td class="text-center font-weight-bold">Jumlah</td>';
        $list_data2 .= '<td class="text-center font-weight-bold">Prioritas</td>';
        $list_data2 .= '</tr>';
        $i = 0;
        foreach ($kriteria as $row) {
            $list_data2 .= '<tr>';
            $list_data2 .= '<td>' . $row['kode_kriteria'] . '</td>';
            $jumlah = 0;
            $ii = 0;
            foreach ($kriteria as $row2) {
                $list_data2 .= '<td class="text-center">' . $matrik_normalisasi[$i][$ii] . '</td>';
                $jumlah += $matrik_normalisasi[$i][$ii];
                $ii++;
            }
            $list_data2 .= '<td class="text-center font-weight-bold">' . $jumlah . '</td>';
            $list_data2 .= '<td class="text-center font-weight-bold">' . $prioritas[$i] . '</td>';
            $list_data2 .= '</tr>';
            $i++;
        }
        // ---
        return $list_data2;
    }

    public function tampil_data_3($matrik_baris, $jumlah_matrik_baris)
    {
        $kriteria = $this->kriteriaModel->findAll();
        // --- matriks penjumlahan setiap baris
        $list_data3 = '';
        $list_data3 .= '<tr><td></td>';
        foreach ($kriteria as $row) {
            $list_data3 .= '<td class="text-center">' . $row['kode_kriteria'] . '</td>';
        }
        $list_data3 .= '<td class="text-center font-weight-bold">Jumlah</td>';
        $list_data3 .= '</tr>';
        $i = 0;
        foreach ($kriteria as $row) {
            $list_data3 .= '<tr>';
            $list_data3 .= '<td>' . $row['kode_kriteria'] . '</td>';
            $ii = 0;
            foreach ($kriteria as $row2) {
                $list_data3 .= '<td class="text-center">' . $matrik_baris[$i][$ii] . '</td>';
                $ii++;
            }
            $list_data3 .= '<td class="text-center font-weight-bold">' . $jumlah_matrik_baris[$i] . '</td>';
            $list_data3 .= '</tr>';
            $i++;
        }
        // ---
        return $list_data3;
    }

    public function tampil_data_4($jumlah_matrik_baris, $prioritas, $hasil_tabel_konsistensi)
    {
        $kriteria = $this->kriteriaModel->findAll();
        // --- perhitungan rasio konsistensi
        $list_data4 = '';
        $list_data4 .= '<tr><td></td>';
        $list_data4 .= '<td class="text-center">Jumlah per Baris</td>';
        $list_data4 .= '<td class="text-center">Prioritas</td>';
        $list_data4 .= '<td class="text-center font-weight-bold">Hasil</td>';
        $list_data4 .= '</tr>';
        $i = 0;
        foreach ($kriteria as $row) {
            $list_data4 .= '<tr>';
            $list_data4 .= '<td>' . $row['kode_kriteria'] . '</td>';
            $list_data4 .= '<td class="text-center">' . $jumlah_matrik_baris[$i] . '</td>';
            $list_data4 .= '<td class="text-center">' . $prioritas[$i] . '</td>';
            $list_data4 .= '<td class="text-center font-weight-bold">' . $hasil_tabel_konsistensi[$i] . '</td>';
            $list_data4 .= '</tr>';
            $i++;
        }
        $jumlah = array_sum($hasil_tabel_konsistensi);
        $n = count($hasil_tabel_konsistensi);
        $lambda_maks = $jumlah / $n;
        $ci = ($lambda_maks - $n) / ($n - 1);
        $ir = [0, 0, 0.58, 0.9, 1.12, 1.24, 1.32, 1.41, 1.45, 1.49, 1.51, 1.48, 1.56, 1.57, 1.59];
        if ($n <= 15) {
            $ir = $ir[$n - 1];
        } else {
            $ir = $ir[14];
        }
        $cr = number_format($ci / $ir, 5);

        $list_data5 = '';
        $list_data5 .= '<table class="table">
    <tr>
        <td width="100">Jumlah</td>
        <td>= ' . $jumlah . '</td>
    </tr>
    <tr>
        <td width="100">n </td>
        <td>= ' . $n . '</td>
    </tr>
    <tr>
        <td width="100">λ maks</td>
        <td>= ' . number_format($lambda_maks, 5) . '</td>
    </tr>
    <tr>
        <td width="100">CI</td>
        <td>= ' . number_format($ci, 5) . '</td>
    </tr>
    <tr>
        <td width="100">CR</td>
        <td>= ' . $cr . '</td>
    </tr>
    <tr>
        <td width="100">CR <= 0.1</td>';
        if ($cr <= 0.1) {
            $list_data5 .= '
        <td>Konsisten</td>';
        } else {
            $list_data5 .= '
        <td>Tidak Konsisten</td>';
        }
        $list_data5 .= '
    </tr>
    </table>';
        // ---
        return [$list_data4, $list_data5];
    }
}