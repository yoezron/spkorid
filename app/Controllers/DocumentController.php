<?php

// ============================================
// DOCUMENT CONTROLLER
// ============================================

// app/Controllers/DocumentController.php
namespace App\Controllers;

class DocumentController extends BaseController
{
    /**
     * AD/ART Document
     */
    public function adArt()
    {
        $data = [
            'title' => 'AD/ART - SPK',
            'document' => $this->getDocument('ad-art')
        ];

        return view('documents/ad_art', $data);
    }

    /**
     * Manifesto
     */
    public function manifesto()
    {
        $data = [
            'title' => 'Manifesto Serikat - SPK',
            'document' => $this->getDocument('manifesto')
        ];

        return view('documents/manifesto', $data);
    }

    /**
     * Sejarah SPK
     */
    public function sejarah()
    {
        $data = [
            'title' => 'Sejarah SPK',
            'document' => $this->getDocument('sejarah')
        ];

        return view('documents/sejarah', $data);
    }

    /**
     * Download document
     */
    public function download($type)
    {
        $allowedTypes = ['ad-art', 'manifesto', 'sejarah', 'panduan-anggota'];

        if (!in_array($type, $allowedTypes)) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan');
        }

        $filepath = ROOTPATH . 'public/documents/' . $type . '.pdf';

        if (!file_exists($filepath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan');
        }

        // Log download if member
        if (session()->get('member_id')) {
            $this->logDocumentDownload($type);
        }

        return $this->response->download($filepath, null);
    }

    /**
     * Get document content from database
     */
    private function getDocument($slug)
    {
        $db = \Config\Database::connect();
        return $db->table('documents')
            ->where('slug', $slug)
            ->where('is_published', 1)
            ->get()
            ->getRowArray();
    }

    /**
     * Log document download
     */
    private function logDocumentDownload($type)
    {
        $db = \Config\Database::connect();
        return $db->table('document_downloads')->insert([
            'member_id' => session()->get('member_id'),
            'document_type' => $type,
            'downloaded_at' => date('Y-m-d H:i:s'),
            'ip_address' => $this->request->getIPAddress()
        ]);
    }
}
