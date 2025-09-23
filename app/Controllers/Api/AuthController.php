<?php
// app/Controllers/Api/AuthController.php
namespace App\Controllers\Api;

use App\Models\UserModel;

class AuthController extends BaseApiController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * API Login
     */
    public function login()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $this->userModel->authenticate($email, $password);

        if (!$user || isset($user['error'])) {
            return $this->failUnauthorized('Invalid credentials');
        }

        // Generate JWT token
        $token = $this->generateToken($user);

        return $this->respond([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'username' => $user['username'],
                    'role' => $user['role_id']
                ]
            ]
        ]);
    }

    /**
     * Refresh token
     */
    public function refreshToken()
    {
        if (!$this->verifyToken()) {
            return $this->failUnauthorized('Invalid token');
        }

        $user = $this->getUserFromToken();
        $newToken = $this->generateToken($user);

        return $this->respond([
            'status' => 'success',
            'data' => [
                'token' => $newToken
            ]
        ]);
    }

    /**
     * Logout
     */
    public function logout()
    {
        // Invalidate token (if using token blacklist)

        return $this->respond([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Generate JWT token
     */
    private function generateToken($user)
    {
        $key = getenv('JWT_SECRET_KEY');
        $iat = time();
        $exp = $iat + (60 * 60 * 24); // 24 hours

        $payload = [
            'iss' => base_url(),
            'aud' => base_url(),
            'iat' => $iat,
            'exp' => $exp,
            'uid' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role_id']
        ];

        // return JWT::encode($payload, $key, 'HS256');

        // Simplified for example
        return base64_encode(json_encode($payload));
    }
}
