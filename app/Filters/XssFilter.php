<?php

// ============================================
// XSS FILTER
// ============================================

// app/Filters/XssFilter.php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class XssFilter implements FilterInterface
{
    /**
     * Clean input data from XSS attacks
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Get all input data
        $data = array_merge(
            $request->getGet() ?? [],
            $request->getPost() ?? []
        );

        // Clean each input
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $cleaned = $this->cleanArray($value);
            } else {
                $cleaned = $this->cleanInput($value);
            }

            // Update the request data
            if ($request->getMethod() === 'post') {
                $_POST[$key] = $cleaned;
            } else {
                $_GET[$key] = $cleaned;
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Add security headers
        $response->setHeader('X-XSS-Protection', '1; mode=block');
        $response->setHeader('X-Content-Type-Options', 'nosniff');
    }

    /**
     * Clean input string
     */
    private function cleanInput($data)
    {
        // Remove NULL characters
        $data = str_replace(chr(0), '', $data);

        // Validate UTF-8
        if (!mb_check_encoding($data, 'UTF-8')) {
            $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        }

        // Remove invisible characters
        $data = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $data);

        // Convert special characters to HTML entities
        $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Remove dangerous tags and attributes
        $data = $this->removeEvilTags($data);

        return $data;
    }

    /**
     * Clean array recursively
     */
    private function cleanArray($array)
    {
        $cleaned = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $cleaned[$key] = $this->cleanArray($value);
            } else {
                $cleaned[$key] = $this->cleanInput($value);
            }
        }
        return $cleaned;
    }

    /**
     * Remove potentially dangerous HTML tags and attributes
     */
    private function removeEvilTags($data)
    {
        // List of evil tags
        $evil_tags = [
            'script',
            'style',
            'iframe',
            'frame',
            'frameset',
            'object',
            'embed',
            'applet',
            'meta',
            'link',
            'base'
        ];

        foreach ($evil_tags as $tag) {
            $data = preg_replace('/<' . $tag . '.*?' . $tag . '>/is', '', $data);
        }

        // Remove evil attributes
        $evil_attributes = [
            'on\w+',
            'xmlns',
            'formaction',
            'form',
            'xlink:href',
            'FSCommand',
            'seekSegmentTime'
        ];

        foreach ($evil_attributes as $attr) {
            $data = preg_replace('/' . $attr . '="[^"]*"/i', '', $data);
            $data = preg_replace('/' . $attr . "='[^']*'/i", '', $data);
        }

        // Remove javascript: and data: protocols
        $data = preg_replace('/javascript:/i', '', $data);
        $data = preg_replace('/data:text\/html/i', '', $data);

        return $data;
    }
}
