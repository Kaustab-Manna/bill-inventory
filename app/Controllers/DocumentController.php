<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DocumentModel;

class DocumentController extends BaseController
{
    protected $documentModel;

    public function __construct()
    {
        $this->documentModel = new DocumentModel();
    }

    public function upload()
    {
        $entityType = $this->request->getPost('entity_type');
        $entityId   = $this->request->getPost('entity_id');
        $title      = trim($this->request->getPost('title') ?? '');
        $file       = $this->request->getFile('document');

        if (!$file || !$file->isValid() || $file->hasMoved()) {
            $errorMsg = $file ? $file->getErrorString() : 'No file uploaded or invalid file.';
            return redirect()->back()->with('error', 'File upload error: ' . $errorMsg);
        }

        // Get file properties before moving
        $fileSize   = $file->getSize();
        $clientName = $file->getClientName();
        $ext        = strtolower($file->getClientExtension());
        $mimeType   = $file->getClientMimeType() ?: $file->getMimeType();

        // Validate size (max 5MB)
        if ($fileSize > 5242880) {
            return redirect()->back()->with('error', 'File is too large. Maximum allowed size is 5MB.');
        }

        // Allowed extensions
        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'gif', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt'];
        if (!in_array($ext, $allowedExtensions)) {
            return redirect()->back()->with('error', "File format (.{$ext}) not supported. Please upload PDF, Word, Excel, CSV, Text, or Images.");
        }

        // Ensure upload directory exists
        $uploadDir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'documents';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $newName = $file->getRandomName();
        $file->move($uploadDir, $newName);

        $docTitle = !empty($title) ? $title : pathinfo($clientName, PATHINFO_FILENAME);

        $this->documentModel->insert([
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'title'       => $docTitle,
            'filename'    => $newName,
            'file_type'   => substr($mimeType, 0, 150),
            'file_size'   => $fileSize,
            'uploaded_by' => session()->get('user_id'),
            'created_at'  => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(previous_url() . '#docs')->with('success', 'Document uploaded successfully.');
    }

    public function delete($id)
    {
        $document = $this->documentModel->find($id);
        if (!$document) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        $path = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . $document->filename;
        if (file_exists($path)) {
            @unlink($path);
        }

        $this->documentModel->delete($id);
        return redirect()->to(previous_url() . '#docs')->with('success', 'Document deleted successfully.');
    }

    public function download($id)
    {
        $document = $this->documentModel->find($id);
        if (!$document) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        $path = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . $document->filename;
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'Physical file not found on server.');
        }

        $ext = pathinfo($document->filename, PATHINFO_EXTENSION);
        $downloadName = $document->title;
        if (!empty($ext) && !str_ends_with(strtolower($downloadName), '.' . strtolower($ext))) {
            $downloadName .= '.' . $ext;
        }

        return $this->response->download($path, null)->setFileName($downloadName);
    }
}
