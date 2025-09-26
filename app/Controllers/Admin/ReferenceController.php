<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RefStatusKepegawaianModel;
use App\Models\RefPemberiGajiModel;
use App\Models\RefRangeGajiModel;
use App\Models\RefProvinsiModel;
use App\Models\RefKotaModel;
use App\Models\RefJenisPTModel;
use App\Models\RefKampusModel;
use App\Models\RefProdiModel;

class ReferenceController extends BaseController
{
    /**
     * Status Kepegawaian Management
     */
    public function statusKepegawaian()
    {
        $model = new RefStatusKepegawaianModel();

        $data = [
            'title' => 'Status Kepegawaian - SPK',
            'items' => $model->findAll()
        ];

        return view('admin/reference/status_kepegawaian', $data);
    }

    public function storeStatusKepegawaian()
    {
        $model = new RefStatusKepegawaianModel();

        $data = [
            'nama_status' => $this->request->getPost('nama_status'),
            'keterangan' => $this->request->getPost('keterangan')
        ];

        $model->insert($data);
        return redirect()->back()->with('success', 'Data berhasil ditambahkan');
    }

    public function updateStatusKepegawaian($id)
    {
        $model = new RefStatusKepegawaianModel();

        $data = [
            'nama_status' => $this->request->getPost('nama_status'),
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        $model->update($id, $data);
        return redirect()->back()->with('success', 'Data berhasil diperbarui');
    }

    public function deleteStatusKepegawaian($id)
    {
        $model = new RefStatusKepegawaianModel();

        // Check if being used
        $db = \Config\Database::connect();
        $used = $db->table('members')->where('status_kepegawaian_id', $id)->countAllResults();

        if ($used > 0) {
            return redirect()->back()->with('error', 'Data tidak dapat dihapus karena sedang digunakan');
        }

        $model->delete($id);
        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }

    /**
     * Pemberi Gaji Management
     */
    public function pemberiGaji()
    {
        $model = new RefPemberiGajiModel();

        $data = [
            'title' => 'Pemberi Gaji - SPK',
            'items' => $model->findAll()
        ];

        return view('admin/reference/pemberi_gaji', $data);
    }

    public function storePemberiGaji()
    {
        $model = new RefPemberiGajiModel();

        $data = [
            'nama_pemberi' => $this->request->getPost('nama_pemberi'),
            'keterangan' => $this->request->getPost('keterangan')
        ];

        $model->insert($data);
        return redirect()->back()->with('success', 'Data berhasil ditambahkan');
    }

    public function updatePemberiGaji($id)
    {
        $model = new RefPemberiGajiModel();

        $data = [
            'nama_pemberi' => $this->request->getPost('nama_pemberi'),
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        $model->update($id, $data);
        return redirect()->back()->with('success', 'Data berhasil diperbarui');
    }

    public function deletePemberiGaji($id)
    {
        $model = new RefPemberiGajiModel();

        // Check if being used
        $db = \Config\Database::connect();
        $used = $db->table('members')->where('pemberi_gaji_id', $id)->countAllResults();

        if ($used > 0) {
            return redirect()->back()->with('error', 'Data tidak dapat dihapus karena sedang digunakan');
        }

        $model->delete($id);
        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }

    /**
     * Range Gaji Management
     */
    public function rangeGaji()
    {
        $model = new RefRangeGajiModel();

        $data = [
            'title' => 'Range Gaji - SPK',
            'items' => $model->findAll()
        ];

        return view('admin/reference/range_gaji', $data);
    }

    public function storeRangeGaji()
    {
        $model = new RefRangeGajiModel();

        $data = [
            'range_gaji' => $this->request->getPost('range_gaji'),
            'min_gaji' => $this->request->getPost('min_gaji'),
            'max_gaji' => $this->request->getPost('max_gaji')
        ];

        $model->insert($data);
        return redirect()->back()->with('success', 'Data berhasil ditambahkan');
    }

    public function updateRangeGaji($id)
    {
        $model = new RefRangeGajiModel();

        $data = [
            'range_gaji' => $this->request->getPost('range_gaji'),
            'min_gaji' => $this->request->getPost('min_gaji'),
            'max_gaji' => $this->request->getPost('max_gaji'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        $model->update($id, $data);
        return redirect()->back()->with('success', 'Data berhasil diperbarui');
    }

    public function deleteRangeGaji($id)
    {
        $model = new RefRangeGajiModel();

        // Check if being used
        $db = \Config\Database::connect();
        $used = $db->table('members')->where('range_gaji_id', $id)->countAllResults();

        if ($used > 0) {
            return redirect()->back()->with('error', 'Data tidak dapat dihapus karena sedang digunakan');
        }

        $model->delete($id);
        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }

    /**
     * Provinsi Management
     */
    public function provinsi()
    {
        $model = new RefProvinsiModel();

        $data = [
            'title' => 'Provinsi - SPK',
            'items' => $model->findAll()
        ];

        return view('admin/reference/provinsi', $data);
    }

    public function storeProvinsi()
    {
        $model = new RefProvinsiModel();

        $data = [
            'nama_provinsi' => $this->request->getPost('nama_provinsi'),
            'kode_provinsi' => $this->request->getPost('kode_provinsi')
        ];

        $model->insert($data);
        return redirect()->back()->with('success', 'Data berhasil ditambahkan');
    }

    public function updateProvinsi($id)
    {
        $model = new RefProvinsiModel();

        $data = [
            'nama_provinsi' => $this->request->getPost('nama_provinsi'),
            'kode_provinsi' => $this->request->getPost('kode_provinsi'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        $model->update($id, $data);
        return redirect()->back()->with('success', 'Data berhasil diperbarui');
    }

    public function deleteProvinsi($id)
    {
        $model = new RefProvinsiModel();

        // Check if has cities
        $db = \Config\Database::connect();
        $hasCities = $db->table('ref_kota')->where('provinsi_id', $id)->countAllResults();

        if ($hasCities > 0) {
            return redirect()->back()->with('error', 'Provinsi tidak dapat dihapus karena memiliki kota/kabupaten');
        }

        $model->delete($id);
        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }

    /**
     * Kota Management
     */
    public function kota()
    {
        $kotaModel = new RefKotaModel();
        $provinsiModel = new RefProvinsiModel();

        $data = [
            'title' => 'Kota/Kabupaten - SPK',
            'items' => $kotaModel->select('ref_kota.*, ref_provinsi.nama_provinsi')
                ->join('ref_provinsi', 'ref_provinsi.id = ref_kota.provinsi_id')
                ->findAll(),
            'provinsi_list' => $provinsiModel->where('is_active', 1)->findAll()
        ];

        return view('admin/reference/kota', $data);
    }

    public function storeKota()
    {
        $model = new RefKotaModel();

        $data = [
            'provinsi_id' => $this->request->getPost('provinsi_id'),
            'nama_kota' => $this->request->getPost('nama_kota'),
            'kode_kota' => $this->request->getPost('kode_kota')
        ];

        $model->insert($data);
        return redirect()->back()->with('success', 'Data berhasil ditambahkan');
    }

    public function updateKota($id)
    {
        $model = new RefKotaModel();

        $data = [
            'provinsi_id' => $this->request->getPost('provinsi_id'),
            'nama_kota' => $this->request->getPost('nama_kota'),
            'kode_kota' => $this->request->getPost('kode_kota'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        $model->update($id, $data);
        return redirect()->back()->with('success', 'Data berhasil diperbarui');
    }

    public function deleteKota($id)
    {
        $model = new RefKotaModel();

        // Check if being used
        $db = \Config\Database::connect();
        $used = $db->table('members')->where('kota_id', $id)->countAllResults();

        if ($used > 0) {
            return redirect()->back()->with('error', 'Data tidak dapat dihapus karena sedang digunakan');
        }

        $model->delete($id);
        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }

    /**
     * Jenis PT Management
     */
    public function jenisPT()
    {
        $model = new RefJenisPTModel();

        $data = [
            'title' => 'Jenis Perguruan Tinggi - SPK',
            'items' => $model->findAll()
        ];

        return view('admin/reference/jenis_pt', $data);
    }

    public function storeJenisPT()
    {
        $model = new RefJenisPTModel();

        $data = [
            'nama_jenis' => $this->request->getPost('nama_jenis'),
            'keterangan' => $this->request->getPost('keterangan')
        ];

        $model->insert($data);
        return redirect()->back()->with('success', 'Data berhasil ditambahkan');
    }

    public function updateJenisPT($id)
    {
        $model = new RefJenisPTModel();

        $data = [
            'nama_jenis' => $this->request->getPost('nama_jenis'),
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        $model->update($id, $data);
        return redirect()->back()->with('success', 'Data berhasil diperbarui');
    }

    public function deleteJenisPT($id)
    {
        $model = new RefJenisPTModel();

        // Check if has kampus
        $db = \Config\Database::connect();
        $hasKampus = $db->table('ref_kampus')->where('jenis_pt_id', $id)->countAllResults();

        if ($hasKampus > 0) {
            return redirect()->back()->with('error', 'Jenis PT tidak dapat dihapus karena memiliki kampus');
        }

        $model->delete($id);
        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }

    /**
     * Kampus Management
     */
    public function kampus()
    {
        $kampusModel = new RefKampusModel();
        $jenisPTModel = new RefJenisPTModel();
        $kotaModel = new RefKotaModel();

        $data = [
            'title' => 'Kampus - SPK',
            'items' => $kampusModel->select('ref_kampus.*, ref_jenis_pt.nama_jenis, ref_kota.nama_kota')
                ->join('ref_jenis_pt', 'ref_jenis_pt.id = ref_kampus.jenis_pt_id', 'left')
                ->join('ref_kota', 'ref_kota.id = ref_kampus.kota_id', 'left')
                ->findAll(),
            'jenis_pt_list' => $jenisPTModel->where('is_active', 1)->findAll(),
            'kota_list' => $kotaModel->where('is_active', 1)->findAll()
        ];

        return view('admin/reference/kampus', $data);
    }

    public function storeKampus()
    {
        $model = new RefKampusModel();

        $data = [
            'jenis_pt_id' => $this->request->getPost('jenis_pt_id'),
            'kode_pt' => $this->request->getPost('kode_pt'),
            'nama_kampus' => $this->request->getPost('nama_kampus'),
            'alamat' => $this->request->getPost('alamat'),
            'kota_id' => $this->request->getPost('kota_id'),
            'website' => $this->request->getPost('website')
        ];

        $model->insert($data);
        return redirect()->back()->with('success', 'Data berhasil ditambahkan');
    }

    public function updateKampus($id)
    {
        $model = new RefKampusModel();

        $data = [
            'jenis_pt_id' => $this->request->getPost('jenis_pt_id'),
            'kode_pt' => $this->request->getPost('kode_pt'),
            'nama_kampus' => $this->request->getPost('nama_kampus'),
            'alamat' => $this->request->getPost('alamat'),
            'kota_id' => $this->request->getPost('kota_id'),
            'website' => $this->request->getPost('website'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        $model->update($id, $data);
        return redirect()->back()->with('success', 'Data berhasil diperbarui');
    }
}
