<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class BirthdayReminder extends BaseController
{
    private string $accessToken;
    private string $phoneNumberId;

    public function __construct()
    {
        $this->accessToken   = env('WHATSAPP_TOKEN');
        $this->phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');
    }

    public function index()
    {
        try {
            $db       = \Config\Database::connect();
            $students = $db->table('students')
                ->where('MONTH(dob)', date('n'))
                ->where('DAY(dob)', date('j'))
               // ->whereNotNull('dob')
               ->where('dob IS NOT NULL')
                ->get()
                ->getResultArray();

            return view('admin/birthday/index', [
                'students' => $students,
            ]);
        } catch (\Throwable $e) {
            return view('admin/setup', ['error' => $e->getMessage()]);
        }
    }

    public function send(int $id)
    {
        try {
            $db      = \Config\Database::connect();
            $student = $db->table('students')->where('id', $id)->get()->getRowArray();

            if (! $student) {
                return redirect()->to(site_url('admin/birthday-reminder'))->with('error', 'Student not found.');
            }

            if (empty($student['phone'])) {
                return redirect()->to(site_url('admin/birthday-reminder'))->with('error', 'Student has no phone number.');
            }

            $result = $this->sendWhatsAppTemplate(
                $student['phone'],
                'birthday_wishes',
                [$student['full_name'], 'Brilient Brains Library']
            );

            $this->saveWaLog($student, 'birthday_wishes', $result);

            if ($result['success']) {
                return redirect()->to(site_url('admin/birthday-reminder'))->with('success', 'Birthday wish sent to ' . esc($student['full_name']) . '.');
            }

            return redirect()->to(site_url('admin/birthday-reminder'))->with('error', 'Failed to send to ' . esc($student['full_name']) . ': ' . ($result['error'] ?? 'Unknown error'));
        } catch (\Throwable $e) {
            return redirect()->to(site_url('admin/birthday-reminder'))->with('error', $e->getMessage());
        }
    }

    public function sendAll()
    {
        try {
            $db       = \Config\Database::connect();
            $students = $db->table('students')
                ->where('MONTH(dob)', date('n'))
                ->where('DAY(dob)', date('j'))
               // ->whereNotNull('dob')
                ->where('dob IS NOT NULL')
                ->get()
                ->getResultArray();

            $sent   = 0;
            $failed = 0;

            foreach ($students as $student) {
                if (empty($student['phone'])) {
                    $failed++;
                    continue;
                }

                $result = $this->sendWhatsAppTemplate(
                    $student['phone'],
                    'birthday_wishes',
                    [$student['full_name'], 'Brilient Brains Library']
                );

                $this->saveWaLog($student, 'birthday_wishes', $result);

                if ($result['success']) {
                    $sent++;
                } else {
                    $failed++;
                }

                sleep(1);
            }

            return redirect()->to(site_url('admin/birthday-reminder'))
                ->with('success', "Birthday wishes sent: $sent succeeded, $failed failed.");
        } catch (\Throwable $e) {
            return redirect()->to(site_url('admin/birthday-reminder'))->with('error', $e->getMessage());
        }
    }

    private function sendWhatsAppTemplate(string $phone, string $templateName, array $params): array
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (strlen($phone) === 10) $phone = '91' . $phone;
        if (strlen($phone) !== 12) return ['success' => false, 'error' => 'Invalid phone'];

        $parameters = [];
        foreach ($params as $param) {
            $parameters[] = ['type' => 'text', 'text' => $param];
        }

        $payload = json_encode([
            'messaging_product' => 'whatsapp',
            'to'                => $phone,
            'type'              => 'template',
            'template'          => [
                'name'       => $templateName,
                'language'   => ['code' => 'en_US'],
                'components' => [[
                    'type'       => 'body',
                    'parameters' => $parameters,
                ]],
            ],
        ]);

        $url = 'https://graph.facebook.com/v22.0/' . $this->phoneNumberId . '/messages';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->accessToken,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            log_message('error', '[LibWA] cURL: ' . $curlError);
            return ['success' => false, 'error' => $curlError];
        }

        if ($httpCode === 200) {
            log_message('info', '[LibWA] Sent OK to ' . $phone);
            return ['success' => true];
        }

        log_message('error', '[LibWA] HTTP ' . $httpCode . ' | ' . $response);
        return ['success' => false, 'error' => 'HTTP ' . $httpCode . ' | ' . $response];
    }

    private function saveWaLog(array $student, string $template, array $result): void
    {
        try {
            \Config\Database::connect()->table('whatsapp_logs')->insert([
                'student_id'    => (int) ($student['id'] ?? 0),
                'student_name'  => $student['full_name'],
                'phone'         => $student['phone'] ?? '',
                'template_name' => $template,
                'status'        => $result['success'] ? 'sent' : 'failed',
                'error_message' => $result['error'] ?? null,
                'sent_at'       => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[LibWA Log] ' . $e->getMessage());
        }
    }
}
