<?php
// ============================================
// API CONTROLLERS
// ============================================

// app/Controllers/Api/BaseApiController.php
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class BaseApiController extends ResourceController
{
    use ResponseTrait;

    protected $format = 'json';

    /**
     * Verify API token
     */
    protected function verifyToken()
    {
        $header = $this->request->getHeader('Authorization');

        if (empty($header)) {
            return false;
        }

        $token = str_replace('Bearer ', '', $header->getValue());

        // Implement JWT verification here
        return $this->validateJWT($token);
    }

    /**
     * Validate JWT token
     */
    private function validateJWT($token)
    {
        // Implementation of JWT validation
        // You can use Firebase JWT library
        try {
            $key = getenv('JWT_SECRET_KEY');
            // $decoded = JWT::decode($token, $key, array('HS256'));
            // return $decoded;

            // Simplified for example
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get user from token
     */
    protected function getUserFromToken()
    {
        $header = $this->request->getHeader('Authorization');
        $token = str_replace('Bearer ', '', $header->getValue());

        // Decode and get user ID from JWT
        // Return user data

        return null;
    }
}
