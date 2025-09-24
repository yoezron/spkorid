<?php
// app/Filters/VerifiedFilter.php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class VerifiedFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $userModel = new \App\Models\UserModel();

        $userId = $session->get('user_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $user = $userModel->find($userId);

        if (!$user || !$user['is_verified']) {
            if ($request->isAJAX()) {
                return response()->setJSON([
                    'status' => false,
                    'message' => 'Email belum diverifikasi'
                ])->setStatusCode(403);
            }

            return redirect()->to('/verify-reminder')->with('warning', 'Email Anda belum diverifikasi. Silakan cek email Anda.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
