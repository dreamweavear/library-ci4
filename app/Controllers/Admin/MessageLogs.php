<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class MessageLogs extends BaseController
{
    public function index()
    {
        try {
            $db      = \Config\Database::connect();
            $perPage = 25;
            $page    = (int) ($this->request->getGet('page') ?? 1);
            if ($page < 1) $page = 1;
            $offset = ($page - 1) * $perPage;

            $total = $db->table('whatsapp_logs')->countAll();
            $logs  = $db->table('whatsapp_logs')
                ->orderBy('id', 'DESC')
                ->limit($perPage, $offset)
                ->get()
                ->getResultArray();

            $totalPages = (int) ceil($total / $perPage);

            return view('admin/message_logs/index', [
                'logs'        => $logs,
                'total'       => $total,
                'page'        => $page,
                'totalPages'  => $totalPages,
                'perPage'     => $perPage,
            ]);
        } catch (\Throwable $e) {
            return view('admin/setup', ['error' => $e->getMessage()]);
        }
    }
}
