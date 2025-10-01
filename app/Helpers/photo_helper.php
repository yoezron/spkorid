<?php

declare(strict_types=1);

if (!function_exists('resolve_user_photo_url')) {
    /**
     * Resolve a usable public URL for a stored user photo path.
     *
     * @param string|null $path    The stored path (relative, absolute or Windows style) to the photo.
     * @param string      $default Relative path to the default avatar inside the public directory.
     */
    function resolve_user_photo_url(?string $path, string $default = 'neptune-assets/images/avatars/avatar.png'): string
    {
        $normalize = static function (?string $value): string {
            if (empty($value)) {
                return '';
            }

            $value = trim($value);
            $value = str_replace('\\', '/', $value);

            return $value;
        };

        $normalized = $normalize($path);

        if ($normalized !== '' && filter_var($normalized, FILTER_VALIDATE_URL)) {
            return $normalized;
        }

        $candidates = [];

        if ($normalized !== '') {
            $trimmed = ltrim($normalized, '/');
            $candidates[] = $trimmed;

            if (stripos($trimmed, 'public/') === 0) {
                $candidates[] = substr($trimmed, strlen('public/'));
            }
        }

        $defaultPath = ltrim($normalize($default), '/');
        $candidates[] = $defaultPath;

        foreach ($candidates as $candidate) {
            if ($candidate === '' || $candidate === false) {
                continue;
            }

            $relative = ltrim(str_replace('\\', '/', (string) $candidate), '/');
            $localPath = FCPATH . ltrim(preg_replace('#^public/#i', '', $relative), '/');

            if (is_file($localPath)) {
                return base_url(ltrim(preg_replace('#^public/#i', '', $relative), '/'));
            }
        }

        return base_url($defaultPath);
    }
}
