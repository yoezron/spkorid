<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Session\Session; // Ditambahkan untuk type hinting
use Psr\Log\LoggerInterface;
use App\Models\MenuModel;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 * class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['auth', 'form', 'url', 'text'];

    /**
     * Session instance.
     */
    protected Session $session; // Type hint yang tepat ditambahkan

    /**
     * Shared data for views
     */
    protected array $data = [];

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // 1. Inisialisasi session dengan type hint yang tepat
        $this->session = service('session');

        // 2. Logika untuk mengambil dan membagikan menu sidebar
        // Cek apakah pengguna sudah login
        if ($this->session->get('logged_in')) {
            $menuModel = new MenuModel();
            $roleId = $this->session->get('role_id');

            // Ambil menu dari model dan simpan ke properti data
            $this->data['sidebar_menus'] = $menuModel->buildMenuTree($roleId);

            // Bagikan data menu ke semua view yang akan di-render oleh controller ini
            // atau turunannya.
            // Data sudah tersedia di $this->data['sidebar_menus'], gunakan saat memanggil view di controller turunan.
        }
    }
    protected function getUserId()
    {
        // Check multiple possible session keys for backward compatibility
        $userId = session()->get('user_id');

        if (!$userId) {
            $userId = session()->get('id');
        }

        if (!$userId) {
            // Check if user is logged in as member
            $memberData = session()->get('member');
            if ($memberData && isset($memberData['user_id'])) {
                $userId = $memberData['user_id'];
            }
        }

        return $userId ? (int) $userId : null;
    }

    /**
     * Get current member ID from session
     * 
     * @return int|null
     */
    protected function getMemberId()
    {
        $memberId = session()->get('member_id');

        if (!$memberId) {
            // Check if member data exists in session
            $memberData = session()->get('member');
            if ($memberData && isset($memberData['id'])) {
                $memberId = $memberData['id'];
            }
        }

        return $memberId ? (int) $memberId : null;
    }

    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    protected function isAuthenticated()
    {
        return $this->getUserId() !== null;
    }

    /**
     * Get current user role
     * 
     * @return string|null
     */
    protected function getUserRole()
    {
        $role = session()->get('role');

        if (!$role) {
            $role = session()->get('user_role');
        }

        return $role;
    }

    /**
     * Require authentication or redirect
     * 
     * @return mixed
     */
    protected function requireAuth()
    {
        if (!$this->isAuthenticated()) {
            return redirect()->to('/login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        return true;
    }

    /**
     * Log user activity
     * 
     * @param string $action
     * @param string $description
     * @return void
     */
    protected function logActivity($action, $description)
    {
        try {
            $db = \Config\Database::connect();
            $db->table('activity_logs')->insert([
                'user_id' => $this->getUserId(),
                'action' => $action,
                'description' => $description,
                'ip_address' => service('request')->getIPAddress(),
                'user_agent' => service('request')->getUserAgent()->getAgentString(),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to log activity: ' . $e->getMessage());
        }
    }
}
