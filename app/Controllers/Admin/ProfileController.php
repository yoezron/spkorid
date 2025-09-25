<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\UserModel;

class ProfileController extends BaseController
{
    protected $memberModel;
    protected $userModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $memberId = session()->get('member_id');
        $userId = session()->get('user_id');

        // Admin mungkin tidak punya member_id
        if ($memberId) {
            $member = $this->memberModel->getMemberWithDetails($memberId);
        } else {
            $member = null;
        }

        $user = $this->userModel->find($userId);

        $data = [
            'title' => 'Profil Saya - SPK',
            'member' => $member,
            'user' => $user
        ];

        return view('admin/profile/index', $data);
    }

    public function edit()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        $data = [
            'title' => 'Edit Profil - SPK',
            'user' => $user
        ];

        return view('admin/profile/edit', $data);
    }

    public function update()
    {
        $userId = session()->get('user_id');

        $rules = [
            'username' => 'required|min_length[3]',
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->userModel->update($userId, [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email')
        ]);

        return redirect()->to('/admin/profile')->with('success', 'Profil berhasil diperbarui');
    }

    public function changePassword()
    {
        $data = [
            'title' => 'Ubah Password - SPK'
        ];

        return view('admin/profile/change_password', $data);
    }

    public function updatePassword()
    {
        $userId = session()->get('user_id');

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $user = $this->userModel->find($userId);

        if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
            return redirect()->back()->with('error', 'Password lama tidak sesuai');
        }

        $this->userModel->update($userId, [
            'password' => password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT)
        ]);

        return redirect()->to('/admin/profile')->with('success', 'Password berhasil diubah');
    }
}
