<?php
// app/Controllers/Api/InformasiController.php
namespace App\Controllers\Api;

use App\Models\InformasiSerikatModel;

class InformasiController extends BaseApiController
{
    protected $informasiModel;

    public function __construct()
    {
        $this->informasiModel = new InformasiSerikatModel();
    }

    /**
     * Get list of informasi
     */
    public function index()
    {
        $kategori = $this->request->getGet('kategori');
        $limit = $this->request->getGet('limit') ?? 10;
        $offset = $this->request->getGet('offset') ?? 0;

        $informasi = $this->informasiModel->getPublished($kategori, $limit);

        return $this->respond([
            'status' => 'success',
            'data' => $informasi,
            'meta' => [
                'total' => $this->informasiModel->where('status', 'published')->countAllResults(),
                'limit' => $limit,
                'offset' => $offset
            ]
        ]);
    }

    /**
     * Get single informasi
     */
    public function view($id)
    {
        $informasi = $this->informasiModel->find($id);

        if (!$informasi || $informasi['status'] !== 'published') {
            return $this->failNotFound('Information not found');
        }

        // Increment view count
        $this->informasiModel->update($id, [
            'view_count' => $informasi['view_count'] + 1
        ]);

        return $this->respond([
            'status' => 'success',
            'data' => $informasi
        ]);
    }
}
