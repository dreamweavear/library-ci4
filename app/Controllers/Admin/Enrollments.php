<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\SeatModel;
use App\Models\StudentModel;

class Enrollments extends BaseController
{
        // line added for email functionaliy starts
                    private string $accessToken;
                private string $phoneNumberId;
                private array $emailConfig = [
                    'protocol'   => 'smtp',
                    'SMTPHost'   => 'mail.aruncomputer.com',
                    'SMTPUser'   => 'admin@aruncomputer.com',
                    'SMTPPass'   => 'Rewa@12345678',
                    'SMTPPort'   => 587,
                    'SMTPCrypto' => 'tls',
                    'mailType'   => 'html',
                    'charset'    => 'utf-8',
                    'wordWrap'   => true,
                    'validate'   => true,
                    'timeout'    => 10,
                    'newline'    => "\r\n"
                ];
        // end here
    // function added for whatsapp functionality starts 
      /*      private string $accessToken;
            private string $phoneNumberId;
    */
            public function __construct()
            {
                $this->accessToken   = env('WHATSAPP_TOKEN');
                $this->phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');
            }
       // end here      
    public function index()
    {
        try {
            $enrollmentModel = new EnrollmentModel();

        $status = strtoupper(trim((string) $this->request->getGet('status')));
        if ($status === '') {
            $status = 'ACTIVE';
        }
        if (! in_array($status, ['ACTIVE', 'ENDED'], true)) {
            $status = 'ACTIVE';
        }

        $rows = $enrollmentModel
            ->select('enrollments.*, students.full_name, students.phone, seats.seat_no, seats.floor')
            ->join('students', 'students.id = enrollments.student_id')
            ->join('seats', 'seats.id = enrollments.seat_id')
            ->where('enrollments.status', $status)
            ->orderBy('enrollments.id', 'DESC')
            ->paginate(25);

        $library = config('Library');

            return view('admin/enrollments/index', [
                'enrollments' => $rows,
                'pager'       => $enrollmentModel->pager,
                'status'      => $status,
                'library'     => $library,
            ]);
        } catch (\Throwable $e) {
            return view('admin/setup', ['error' => $e->getMessage()]);
        }
    }

    public function exportCsv()
    {
        try {
            $enrollmentModel = new EnrollmentModel();
            $status = strtoupper(trim((string) $this->request->getGet('status')));
            if (! in_array($status, ['ACTIVE', 'ENDED'], true)) $status = 'ACTIVE';

            $rows = $enrollmentModel
                ->select('enrollments.*, students.full_name, students.phone, seats.seat_no, seats.floor')
                ->join('students', 'students.id = enrollments.student_id')
                ->join('seats', 'seats.id = enrollments.seat_id')
                ->where('enrollments.status', $status)
                ->orderBy('enrollments.id', 'DESC')
                ->findAll();

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="enrollments_' . strtolower($status) . '_' . date('Y-m-d') . '.csv"');
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Student', 'Phone', 'Seat', 'Floor', 'Plan', 'Fee', 'Start Date', 'End Date', 'Status']);
            foreach ($rows as $e) {
                fputcsv($out, [
                    $e['id'], $e['full_name'], $e['phone'] ?? '',
                    $e['seat_no'], $e['floor'],
                    $e['plan'] . (! empty($e['half_day_slot']) ? ' (' . $e['half_day_slot'] . ')' : ''),
                    $e['fee'], $e['start_date'], $e['end_date'] ?? '', $e['status'],
                ]);
            }
            fclose($out);
            exit;
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
            return redirect()->back()->with('error', 'Export failed.');
        }
    }

    public function new()
    {
        try {
            $studentModel = new StudentModel();
            $seatModel = new SeatModel();

        $students = $studentModel->orderBy('full_name', 'ASC')->findAll();

        // Only show seats that have no active enrollment
        $db = \Config\Database::connect();
        $occupiedSeatIds = $db->table('enrollments')
            ->select('seat_id')
            ->where('status', 'ACTIVE')
            ->get()->getResultArray();
        $occupiedIds = array_column($occupiedSeatIds, 'seat_id');

        $seatBuilder = $seatModel->orderBy('seat_no', 'ASC');
        if (! empty($occupiedIds)) {
            $seatBuilder = $seatBuilder->whereNotIn('id', $occupiedIds);
        }
        $seats = $seatBuilder->findAll();

        $library = config('Library');

            return view('admin/enrollments/form', [
                'students'             => $students,
                'seats'                => $seats,
                'library'              => $library,
                'preselectedStudentId' => (int) $this->request->getGet('student_id'),
                'errors'               => session()->getFlashdata('errors') ?? [],
            ]);
        } catch (\Throwable $e) {
            return view('admin/setup', ['error' => $e->getMessage()]);
        }
    }

    public function create()
    {
        try {
        $library = config('Library');

        $rules = [
            'student_id'    => 'required|is_natural_no_zero',
            'seat_id'       => 'required|is_natural_no_zero',
            'plan'          => 'required|in_list[' . implode(',', $library->plans) . ']',
            'half_day_slot' => 'permit_empty|in_list[' . implode(',', $library->halfDaySlots) . ']',
            'start_date'    => 'required|valid_date[Y-m-d]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $studentId = (int) $this->request->getPost('student_id');
        $seatId = (int) $this->request->getPost('seat_id');
        $plan = strtoupper(trim((string) $this->request->getPost('plan')));
        $halfDaySlot = strtoupper(trim((string) $this->request->getPost('half_day_slot')));
        $startDate = (string) $this->request->getPost('start_date');

        if ($plan === 'HALF_DAY' && $halfDaySlot === '') {
            return redirect()->back()->withInput()->with('errors', ['half_day_slot' => 'Half-day slot is required for Half Day plan.']);
        }
        if ($plan === 'FULL_DAY') {
            $halfDaySlot = null;
        }

        $studentModel = new StudentModel();
        $seatModel = new SeatModel();
        $enrollmentModel = new EnrollmentModel();

        $student = $studentModel->find($studentId);
        if (! $student) {
            return redirect()->back()->withInput()->with('errors', ['student_id' => 'Student not found.']);
        }

        $seat = $seatModel->find($seatId);
        if (! $seat) {
            return redirect()->back()->withInput()->with('errors', ['seat_id' => 'Seat not found.']);
        }

        if ($seat['seat_no'] < 1 || $seat['seat_no'] > 100) {
            return redirect()->back()->withInput()->with('errors', ['seat_id' => 'Seat number must be between 1 and 100.']);
        }

        if ($enrollmentModel->getSeatConflict($seatId, $plan, $halfDaySlot)) {
            if ($plan === 'FULL_DAY') {
                return redirect()->back()->withInput()->with('errors', ['seat_id' => 'This seat already has an active enrollment (Full/Half day).']);
            }
            return redirect()->back()->withInput()->with('errors', ['seat_id' => 'This seat is already occupied for the selected half-day slot, or is occupied for Full Day.']);
        }

        if ($enrollmentModel->getActiveByStudentId($studentId)) {
            return redirect()->back()->withInput()->with('errors', ['student_id' => 'This student already has an active seat. End the current enrollment first.']);
        }

        $fee = 0;
        if ($plan === 'FULL_DAY') {
            $floor = strtoupper((string) $seat['floor']);
            $fee = (int) ($library->fees['FULL_DAY'][$floor] ?? 0);
        } else {
            $fee = (int) $library->fees['HALF_DAY'];
        }

        $id = $enrollmentModel->insert([
            'student_id'    => $studentId,
            'seat_id'       => $seatId,
            'plan'          => $plan,
            'half_day_slot' => $halfDaySlot,
            'fee'           => $fee,
            'start_date'    => $startDate,
            'end_date'      => null,
            'status'        => 'ACTIVE',
        ]);

    // line below is add for whatsapp functionalaiye starts
    // WhatsApp message bhejo
        if (!empty($student['phone'])) {
            $this->sendEnrollmentWhatsApp([
                'full_name' => $student['full_name'],
                'phone'     => $student['phone'],
                'seat_no'   => $seat['seat_no'],
            ]);
        }
    // end here

    // Email bhejo (optional - sirf tab jab email ho) start here
if (!empty($student['email'])) {
    $emailResult = $this->sendEnrollmentEmail($student, $seat, $startDate);
    if (!$emailResult['success']) {
        log_message('error', '[LibEmail] Enrollment #' . $id . ' | ' . ($emailResult['error'] ?? ''));
    }
}
// email code end here


        return redirect()->to(site_url('admin/enrollments'))->with('success', 'Seat allotted (Enrollment #' . $id . ').');
    } catch (\Throwable $e) {
        return redirect()->back()->withInput()->with('error', $e->getMessage());
    }
}

    /**
     * GET admin/enrollments/change-seat/(:num)
     * Show the change-seat form for a student.
     */
    public function changeSeatForm(int $studentId)
    {
        try {
            $studentModel    = new StudentModel();
            $enrollmentModel = new EnrollmentModel();
            $seatModel       = new SeatModel();

            $student = $studentModel->find($studentId);
            if (! $student) {
                return redirect()->to(site_url('admin/students'))->with('error', 'Student not found.');
            }

            $current = $enrollmentModel
                ->select('enrollments.*, seats.seat_no, seats.floor')
                ->join('seats', 'seats.id = enrollments.seat_id')
                ->where('enrollments.student_id', $studentId)
                ->where('enrollments.status', 'ACTIVE')
                ->first();

            if (! $current) {
                return redirect()->to(site_url('admin/students/' . $studentId))
                    ->with('error', 'This student has no active seat. Use "Allot Seat" first.');
            }

            // Build list of seats available for the same plan/slot (excluding current seat)
            $allSeats     = $seatModel->orderBy('seat_no', 'ASC')->findAll();
            $plan         = $current['plan'];
            $halfDaySlot  = $current['half_day_slot'] ?? null;
            $availableSeats = [];

            foreach ($allSeats as $seat) {
                if ((int) $seat['id'] === (int) $current['seat_id']) {
                    continue; // skip current seat
                }
                if (! $enrollmentModel->getSeatConflict((int) $seat['id'], $plan, $halfDaySlot)) {
                    $availableSeats[] = $seat;
                }
            }

            return view('admin/enrollments/change_seat', [
                'student'        => $student,
                'current'        => $current,
                'availableSeats' => $availableSeats,
                'errors'         => session()->getFlashdata('errors') ?? [],
            ]);
        } catch (\Throwable $e) {
            return view('admin/setup', ['error' => $e->getMessage()]);
        }
    }

    /**
     * POST admin/enrollments/change-seat/(:num)
     * Execute the seat change inside a DB transaction.
     */
    public function changeSeat(int $studentId)
    {
        try {
            $studentModel    = new StudentModel();
            $enrollmentModel = new EnrollmentModel();
            $seatModel       = new SeatModel();

            $student = $studentModel->find($studentId);
            if (! $student) {
                return redirect()->to(site_url('admin/students'))->with('error', 'Student not found.');
            }

            $current = $enrollmentModel
                ->where('student_id', $studentId)
                ->where('status', 'ACTIVE')
                ->first();

            if (! $current) {
                return redirect()->to(site_url('admin/students/' . $studentId))
                    ->with('error', 'No active enrollment found for this student.');
            }

            $newSeatId  = (int) $this->request->getPost('new_seat_id');
            $changeDate = trim((string) $this->request->getPost('change_date')) ?: date('Y-m-d');
            $reason     = trim((string) $this->request->getPost('reason'));

            // Validate
            if ($newSeatId <= 0) {
                return redirect()->back()->withInput()->with('errors', ['new_seat_id' => 'Please select a new seat.']);
            }
            if ($newSeatId === (int) $current['seat_id']) {
                return redirect()->back()->withInput()->with('errors', ['new_seat_id' => 'New seat must be different from the current seat.']);
            }

            $newSeat = $seatModel->find($newSeatId);
            if (! $newSeat) {
                return redirect()->back()->withInput()->with('errors', ['new_seat_id' => 'Selected seat not found.']);
            }

            // Check seat availability for the same plan
            $plan        = $current['plan'];
            $halfDaySlot = $current['half_day_slot'] ?? null;

            if ($enrollmentModel->getSeatConflict($newSeatId, $plan, $halfDaySlot)) {
                return redirect()->back()->withInput()->with('errors', ['new_seat_id' => 'Selected seat is no longer available. Please choose another.']);
            }

            // Transaction: end current → create new
            $db = \Config\Database::connect();
            $db->transBegin();

            try {
                $enrollmentModel->update((int) $current['id'], [
                    'status'   => 'ENDED',
                    'end_date' => $changeDate,
                ]);

                $enrollmentModel->insert([
                    'student_id'    => $studentId,
                    'seat_id'       => $newSeatId,
                    'plan'          => $plan,
                    'half_day_slot' => $halfDaySlot,
                    'fee'           => $current['fee'],
                    'start_date'    => $changeDate,
                    'end_date'      => null,
                    'status'        => 'ACTIVE',
                ]);

                $db->transCommit();
            } catch (\Throwable $inner) {
                $db->transRollback();
                throw $inner;
            }

            $oldSeatNo = $current['seat_no'] ?? $current['seat_id'];
            $newSeatNo = $newSeat['seat_no'];

            return redirect()->to(site_url('admin/students/' . $studentId))
                ->with('success', "Seat successfully changed from #$oldSeatNo to #$newSeatNo." . ($reason !== '' ? " Reason: $reason" : ''));
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function end(int $id)
    {
        try {
            $enrollmentModel = new EnrollmentModel();
            $row = $enrollmentModel->find($id);
            if (! $row) {
                return redirect()->back()->with('error', 'Enrollment not found.');
            }

            if ($row['status'] !== 'ACTIVE') {
                return redirect()->back()->with('error', 'Enrollment is already ended.');
            }

            $enrollmentModel->update($id, [
                'status'   => 'ENDED',
                'end_date' => date('Y-m-d'),
            ]);

            return redirect()->back()->with('success', 'Enrollment ended. Seat is now available.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

// function added for whatsapp functionality  starts
private function sendEnrollmentWhatsApp(array $student): array
{
    $phone = preg_replace('/\D/', '', $student['phone']);
    if (strlen($phone) === 10) $phone = '91' . $phone;
    if (strlen($phone) !== 12)  return ['success' => false, 'error' => 'Invalid phone'];

    $payload = json_encode([
        'messaging_product' => 'whatsapp',
        'to'                => $phone,
        'type'              => 'template',
        'template'          => [
            'name'       => 'library_admission',
            'language'   => ['code' => 'en_US'],
            'components' => [[
                'type'       => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => $student['full_name']],
                    ['type' => 'text', 'text' => (string)$student['seat_no']],
                    ['type' => 'text', 'text' => date('d-m-Y')],  // aaj ki date
                ]
            ]]
        ]
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
   // return ['success' => false, 'error' => 'HTTP ' . $httpCode];
  // upar ki line whatsapp page not found check karneke liye change kiya 
     return ['success' => false, 'error' => 'HTTP ' . $httpCode . ' | ' . $response];
}

private function saveWaLog(array $student, string $template, array $result): void
{
    try {
        \Config\Database::connect()->table('whatsapp_logs')->insert([
            'student_id'    => 0,
            'student_name'  => $student['full_name'],
            'phone'         => $student['phone'],
            'template_name' => $template,
            'status'        => $result['success'] ? 'sent' : 'failed',
            'error_message' => $result['error'] ?? null,
            'sent_at'       => date('Y-m-d H:i:s'),
        ]);
    } catch (\Throwable $e) {
        log_message('error', '[LibWA Log] ' . $e->getMessage());
    }
}
/*
public function testWa()
{
    $result = $this->sendEnrollmentWhatsApp([
        'full_name' => 'Om Prakash Ji',
        'phone'     => '9926542408',
        'seat_no'   => '42',
    ]);

    echo '<pre style="font-size:16px; padding:20px;">';
    echo 'Token (first 25): ' . substr($this->accessToken, 0, 25) . '...' . "\n";
    echo 'Phone ID: ' . $this->phoneNumberId . "\n\n";
    echo 'Result: ';
    print_r($result);
    echo '</pre>';
}
*/
//end here

        // emale functionaliyt start here 
        private function sendEnrollmentEmail(array $student, array $seat, string $startDate): array
{
    try {
        $email = \Config\Services::email($this->emailConfig);

        $email->setFrom('admin@aruncomputer.com', 'Brilient Brains Library');
        $email->setTo($student['email']);
        $email->setSubject('Seat Allotment Confirmation - Brilient Brains Library');
        $email->setMessage($this->createEnrollmentEmailTemplate($student, $seat, $startDate));

        if ($email->send(false)) {
            log_message('info', '[LibEmail] Sent to ' . $student['email']);
            return ['success' => true];
        }

        log_message('error', '[LibEmail] Failed to ' . $student['email']);
        return ['success' => false, 'error' => 'Email send failed'];

    } catch (\Exception $e) {
        log_message('error', '[LibEmail] Exception: ' . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

private function createEnrollmentEmailTemplate(array $student, array $seat, string $startDate): string
{
    $institutePhone   = '9926542408';
    $instituteAddress = 'Alld. Road, Urrhat, Rewa (M.P.)';
    $websiteUrl       = 'www.brilliantbrains.vindhy.com';
    $loginUrl         = base_url('student/login');

    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:20px; }
            .container { background:#fff; max-width:600px; margin:0 auto; border-radius:10px; overflow:hidden; box-shadow:0 0 15px rgba(0,0,0,0.1); }
            .header { background:linear-gradient(135deg, #1a73e8, #0d47a1); color:#fff; padding:25px; text-align:center; }
            .header h1 { margin:0; font-size:22px; }
            .header p  { margin:5px 0 0; font-size:14px; opacity:0.9; }
            .body { padding:25px; }
            .info-box { background:#f0f7ff; border-left:4px solid #1a73e8; padding:15px; margin:20px 0; border-radius:0 8px 8px 0; }
            .info-box p { margin:6px 0; font-size:14px; }
            .login-box { background:#e8f5e9; border:1px solid #a5d6a7; padding:15px; border-radius:8px; margin:20px 0; }
            .login-box p { margin:6px 0; font-size:14px; }
            .footer { background:#f9f9f9; padding:15px; text-align:center; font-size:12px; color:#888; border-top:1px solid #eee; }
            .badge { display:inline-block; background:#1a73e8; color:#fff; padding:3px 10px; border-radius:20px; font-size:13px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>📚 Brilient Brains Library</h1>
                <p>Seat Allotment Confirmation</p>
            </div>
            <div class='body'>
                <p>Dear <strong>{$student['full_name']}</strong>,</p>
                <p>Aapka seat allotment successfully ho gaya hai! 🎉</p>

                <div class='info-box'>
                    <p>🪑 <strong>Seat No:</strong> <span class='badge'>{$seat['seat_no']}</span></p>
                    <p>🏢 <strong>Floor:</strong> {$seat['floor']}</p>
                    <p>📅 <strong>Start Date:</strong> " . date('d-m-Y', strtotime($startDate)) . "</p>
                    <p>📋 <strong>Plan:</strong> Full Day / Half Day</p>
                </div>

                <div class='login-box'>
                    <p>🔐 <strong>Student Login:</strong></p>
                    <p>Username: <strong>{$student['phone']}</strong></p>
                    <p>Password: <strong>student123</strong></p>
                    <p>🌐 <a href='{$loginUrl}'>{$loginUrl}</a></p>
                </div>

                <p>📍 <strong>Address:</strong> {$instituteAddress}</p>
                <p>📞 <strong>Contact:</strong> {$institutePhone}</p>
                <p>🌐 <strong>Website:</strong> {$websiteUrl}</p>

                <p style='margin-top:20px;'>Study hard - Success zaroor milegi! 💪</p>
            </div>
            <div class='footer'>
                &copy; " . date('Y') . " Brilient Brains Library, Rewa (M.P.)
            </div>
        </div>
    </body>
    </html>
    ";
}

        // email functionality end here


}
