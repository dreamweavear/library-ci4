<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\PaymentModel;
use App\Models\StudentModel;

class Fees extends BaseController
{
    public function index()
    {
        try {
            $paymentModel = new PaymentModel();

            $today = date('Y-m-d');
            $month = date('Y-m');

            $todayTotal = (int) ($paymentModel->selectSum('amount', 'amt')->where('paid_on', $today)->first()['amt'] ?? 0);
            $monthTotal = (int) ($paymentModel->selectSum('amount', 'amt')->like('paid_on', $month, 'after')->first()['amt'] ?? 0);

            $recent = $paymentModel
                ->select('payments.*, students.full_name, students.phone')
                ->join('students', 'students.id = payments.student_id')
                ->orderBy('payments.id', 'DESC')
                ->findAll(25);

            return view('admin/fees/index', [
                'title' => 'Fees Collection',
                'todayTotal' => $todayTotal,
                'monthTotal' => $monthTotal,
                'recent' => $recent,
            ]);
        } catch (\Throwable $e) {
            return view('admin/setup', ['error' => $e->getMessage()]);
        }
    }

    public function collect()
    {
        try {
            $studentModel = new StudentModel();
            $enrollmentModel = new EnrollmentModel();
            $paymentModel = new PaymentModel();

            $students = $studentModel->orderBy('full_name', 'ASC')->findAll();

            if ($this->request->getMethod(true) === 'POST') {
                $rules = [
                    'student_id'     => 'required|is_natural_no_zero',
                    'type'           => 'required|in_list[MONTHLY,ADMISSION]',
                    'for_month'      => 'permit_empty|max_length[7]',
                    'amount'         => 'required|is_natural_no_zero',
                    'paid_on'        => 'required|valid_date[Y-m-d]',
                    'notes'          => 'permit_empty|max_length[255]',
                    'receipt_number' => 'permit_empty|max_length[50]',
                ];

                if (! $this->validate($rules)) {
                    return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
                }

                $studentId     = (int) $this->request->getPost('student_id');
                $type          = strtoupper(trim((string) $this->request->getPost('type')));
                $forMonth      = trim((string) $this->request->getPost('for_month')) ?: null;
                $amount        = (int) $this->request->getPost('amount');
                $paidOn        = (string) $this->request->getPost('paid_on');
                $notes         = trim((string) $this->request->getPost('notes')) ?: null;
                $receiptNumber = trim((string) $this->request->getPost('receipt_number')) ?: null;

                $student = $studentModel->find($studentId);
                if (! $student) {
                    return redirect()->back()->withInput()->with('error', 'Student not found.');
                }

                $enrollmentId = null;
                if ($type === 'MONTHLY') {
                    $active = $enrollmentModel->where('student_id', $studentId)->where('status', 'ACTIVE')->first();
                    if (! $active) {
                        return redirect()->back()->withInput()->with('error', 'No active enrollment found for monthly fee.');
                    }
                    $enrollmentId = (int) $active['id'];

                    if (! $forMonth || ! preg_match('/^\\d{4}-\\d{2}$/', $forMonth)) {
                        return redirect()->back()->withInput()->with('error', 'Please select a valid month (YYYY-MM).');
                    }

                    $exists = $paymentModel
                        ->where('student_id', $studentId)
                        ->where('enrollment_id', $enrollmentId)
                        ->where('type', 'MONTHLY')
                        ->where('for_month', $forMonth)
                        ->first();
                    if ($exists) {
                        return redirect()->back()->withInput()->with('error', 'Monthly fee already collected for this month.');
                    }
                } else {
                    $forMonth = null;
                }

                // Generate unique receipt.
                $receipt = $paymentModel->generateReceiptNo();
                while ($paymentModel->where('receipt_no', $receipt)->first()) {
                    $receipt = $paymentModel->generateReceiptNo();
                }

                $paymentId = $paymentModel->insert([
                    'student_id'     => $studentId,
                    'enrollment_id'  => $enrollmentId,
                    'type'           => $type,
                    'for_month'      => $forMonth,
                    'amount'         => $amount,
                    'paid_on'        => $paidOn,
                    'receipt_no'     => $receipt,
                    'receipt_number' => $receiptNumber,
                    'notes'          => $notes,
                ]);

                return redirect()->to(site_url('admin/fees/receipt/' . (int) $paymentId))->with('success', 'Fee receipt generated.');
            }

            return view('admin/fees/collect', [
                'title'          => 'Collect Fee',
                'students'       => $students,
                'defaultMonth'   => date('Y-m'),
                'defaultPaidOn'  => date('Y-m-d'),
                'errors'         => session()->getFlashdata('errors') ?? [],
            ]);
        } catch (\Throwable $e) {
            return view('admin/setup', ['error' => $e->getMessage()]);
        }
    }

    public function pending()
    {
        try {
            $enrollmentModel = new EnrollmentModel();
            $paymentModel = new PaymentModel();

            $rows = $enrollmentModel
                ->select('enrollments.*, students.full_name, students.phone, seats.seat_no, seats.floor')
                ->join('students', 'students.id = enrollments.student_id')
                ->join('seats', 'seats.id = enrollments.seat_id')
                ->where('enrollments.status', 'ACTIVE')
                ->orderBy('seats.seat_no', 'ASC')
                ->findAll();

            $currentMonth = date('Y-m');
            $report = [];

            foreach ($rows as $r) {
                $startMonth = substr((string) $r['start_date'], 0, 7);
                if (! preg_match('/^\\d{4}-\\d{2}$/', $startMonth)) {
                    continue;
                }
                // ← YAHAN ADD KARO
// Current month admission wale skip karo
//if ($startMonth === $currentMonth) {
  //  continue;
//}



                $monthsDue = $this->monthsBetweenInclusive($startMonth, $currentMonth);
                $monthlyFee = (int) ($r['fee'] ?? 0);
                $dueAmount = $monthsDue * $monthlyFee;

                $paid = (int) ($paymentModel
                    ->selectSum('amount', 'amt')
                    ->where('enrollment_id', (int) $r['id'])
                    ->where('type', 'MONTHLY')
                    ->first()['amt'] ?? 0);

               $admissionPaid = (int) ($paymentModel
    ->selectSum('amount', 'amt')
    ->where('student_id', (int) $r['student_id'])
    ->where('type', 'ADMISSION')
    ->first()['amt'] ?? 0);

$pending = max(0, $dueAmount - ($paid + $admissionPaid));

                if ($pending > 0) {
                    $report[] = [
                        'enrollment' => $r,
                        'monthsDue' => $monthsDue,
                        'monthlyFee' => $monthlyFee,
                        'dueAmount' => $dueAmount,
                        'paidAmount' => $paid,
                        'pendingAmount' => $pending,
                        'fromMonth' => $startMonth,
                        'toMonth' => $currentMonth,
                    ];
                }
            }

            usort($report, static function ($a, $b) {
                return ((int)($a['enrollment']['seat_no'] ?? 0)) <=> ((int)($b['enrollment']['seat_no'] ?? 0));
            });

            return view('admin/fees/pending', [
                'title' => 'Fees Pending Report',
                'report' => $report,
                'currentMonth' => $currentMonth,
            ]);
        } catch (\Throwable $e) {
            return view('admin/setup', ['error' => $e->getMessage()]);
        }
    }

    public function receipt(int $id)
    {
        try {
            $paymentModel = new PaymentModel();
            $row = $paymentModel
                ->select('payments.*, students.full_name, students.phone, students.admission_date, seats.seat_no, seats.floor, enrollments.plan, enrollments.half_day_slot')
                ->join('students', 'students.id = payments.student_id')
                ->join('enrollments', 'enrollments.id = payments.enrollment_id', 'left')
                ->join('seats', 'seats.id = enrollments.seat_id', 'left')
                ->where('payments.id', $id)
                ->first();

            if (! $row) {
                return redirect()->to(site_url('admin/fees'))->with('error', 'Receipt not found.');
            }

            return view('admin/fees/receipt', [
                'title' => 'Fee Receipt',
                'payment' => $row,
            ]);
        } catch (\Throwable $e) {
            return view('admin/setup', ['error' => $e->getMessage()]);
        }
    }

    public function exportCsv()
    {
        try {
            $paymentModel = new PaymentModel();
            $rows = $paymentModel
                ->select('payments.*, students.full_name, students.phone')
                ->join('students', 'students.id = payments.student_id')
                ->orderBy('payments.id', 'DESC')
                ->findAll();

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="fees_' . date('Y-m-d') . '.csv"');
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Receipt#', 'Manual Receipt#', 'Student', 'Phone', 'Type', 'For Month', 'Amount', 'Paid On', 'Notes']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r['receipt_no'],
                    $r['receipt_number'] ?? '',
                    $r['full_name'],
                    $r['phone'] ?? '',
                    $r['type'],
                    $r['for_month'] ?? '',
                    $r['amount'],
                    $r['paid_on'],
                    $r['notes'] ?? '',
                ]);
            }
            fclose($out);
            exit;
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
            return redirect()->back()->with('error', 'Export failed.');
        }
    }

    public function exportPendingCsv()
    {
        try {
            $enrollmentModel = new EnrollmentModel();
            $paymentModel    = new PaymentModel();

            $rows = $enrollmentModel
                ->select('enrollments.*, students.full_name, students.phone, seats.seat_no, seats.floor')
                ->join('students', 'students.id = enrollments.student_id')
                ->join('seats', 'seats.id = enrollments.seat_id')
                ->where('enrollments.status', 'ACTIVE')
                ->orderBy('enrollments.id', 'DESC')
                ->findAll();

            $currentMonth = date('Y-m');
            $report = [];
            foreach ($rows as $r) {
                $startMonth = substr((string) $r['start_date'], 0, 7);
                if (! preg_match('/^\d{4}-\d{2}$/', $startMonth)) continue;
                $monthsDue  = $this->monthsBetweenInclusive($startMonth, $currentMonth);
                $monthlyFee = (int) ($r['fee'] ?? 0);
                $dueAmount  = $monthsDue * $monthlyFee;
                $paid       = (int) ($paymentModel->selectSum('amount', 'amt')->where('enrollment_id', (int) $r['id'])->where('type', 'MONTHLY')->first()['amt'] ?? 0);
                $admPaid    = (int) ($paymentModel->selectSum('amount', 'amt')->where('student_id', (int) $r['student_id'])->where('type', 'ADMISSION')->first()['amt'] ?? 0);
                $pending    = max(0, $dueAmount - ($paid + $admPaid));
                if ($pending > 0) {
                    $report[] = array_merge($r, ['monthlyFee' => $monthlyFee, 'dueAmount' => $dueAmount, 'paidAmount' => $paid, 'pendingAmount' => $pending, 'fromMonth' => $startMonth, 'toMonth' => $currentMonth]);
                }
            }
            usort($report, static fn($a, $b) => $b['pendingAmount'] <=> $a['pendingAmount']);

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="pending_fees_' . date('Y-m-d') . '.csv"');
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Student', 'Phone', 'Seat', 'Floor', 'Plan', 'From', 'To', 'Monthly Fee', 'Due', 'Paid', 'Pending']);
            foreach ($report as $r) {
                fputcsv($out, [
                    $r['full_name'], $r['phone'] ?? '', $r['seat_no'], $r['floor'],
                    $r['plan'] . (! empty($r['half_day_slot']) ? ' (' . $r['half_day_slot'] . ')' : ''),
                    $r['fromMonth'], $r['toMonth'],
                    $r['monthlyFee'], $r['dueAmount'], $r['paidAmount'], $r['pendingAmount'],
                ]);
            }
            fclose($out);
            exit;
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
            return redirect()->back()->with('error', 'Export failed.');
        }
    }

    public function report()
    {
        try {
            $db = \Config\Database::connect();

            // Available years (from actual payments data)
            $yearRows = $db->query('SELECT DISTINCT YEAR(paid_on) AS yr FROM payments ORDER BY yr DESC')->getResultArray();
            $availableYears = array_column($yearRows, 'yr');
            if (empty($availableYears)) {
                $availableYears = [(int) date('Y')];
            }

            $year = (int) ($this->request->getGet('year') ?: date('Y'));
            if (! in_array($year, array_map('intval', $availableYears), true)) {
                $year = (int) $availableYears[0];
            }

            // Monthly breakdown: MONTHLY + ADMISSION split
            $rawRows = $db->query("
                SELECT
                    MONTH(paid_on)                                                AS mon,
                    COUNT(*)                                                      AS payment_count,
                    SUM(CASE WHEN type = 'MONTHLY'   THEN amount ELSE 0 END)     AS monthly_amt,
                    SUM(CASE WHEN type = 'ADMISSION' THEN amount ELSE 0 END)     AS admission_amt,
                    SUM(amount)                                                   AS row_total
                FROM payments
                WHERE YEAR(paid_on) = ?
                GROUP BY MONTH(paid_on)
                ORDER BY MONTH(paid_on)
            ", [$year])->getResultArray();

            // Index by month number for easy lookup
            $byMonth = [];
            foreach ($rawRows as $r) {
                $byMonth[(int) $r['mon']] = $r;
            }

            $monthNames = ['', 'January', 'February', 'March', 'April', 'May', 'June',
                           'July', 'August', 'September', 'October', 'November', 'December'];

            // Build full 12-month array
            $months = [];
            for ($m = 1; $m <= 12; $m++) {
                $row = $byMonth[$m] ?? ['payment_count' => 0, 'monthly_amt' => 0, 'admission_amt' => 0, 'row_total' => 0];
                $months[$m] = [
                    'name'          => $monthNames[$m],
                    'payment_count' => (int) $row['payment_count'],
                    'monthly_amt'   => (int) $row['monthly_amt'],
                    'admission_amt' => (int) $row['admission_amt'],
                    'row_total'     => (int) $row['row_total'],
                ];
            }

            $yearTotal     = array_sum(array_column($months, 'row_total'));
            $yearPayments  = array_sum(array_column($months, 'payment_count'));
            $maxMonthAmt   = max(array_column($months, 'row_total')) ?: 1;

            // Best month (highest total)
            $bestMonthNum  = 1;
            $bestMonthAmt  = 0;
            foreach ($months as $num => $m) {
                if ($m['row_total'] > $bestMonthAmt) {
                    $bestMonthAmt = $m['row_total'];
                    $bestMonthNum = $num;
                }
            }

            return view('admin/fees/report', [
                'title'          => 'Collection Report',
                'year'           => $year,
                'availableYears' => $availableYears,
                'months'         => $months,
                'yearTotal'      => $yearTotal,
                'yearPayments'   => $yearPayments,
                'maxMonthAmt'    => $maxMonthAmt,
                'bestMonthName'  => $monthNames[$bestMonthNum],
                'bestMonthAmt'   => $bestMonthAmt,
            ]);
        } catch (\Throwable $e) {
            return view('admin/setup', ['error' => $e->getMessage()]);
        }
    }

    public function reportExportCsv()
    {
        try {
            $db = \Config\Database::connect();
            $year = (int) ($this->request->getGet('year') ?: date('Y'));

            $rows = $db->query("
                SELECT
                    MONTH(paid_on) AS mon,
                    COUNT(*) AS payment_count,
                    SUM(CASE WHEN type = 'MONTHLY'   THEN amount ELSE 0 END) AS monthly_amt,
                    SUM(CASE WHEN type = 'ADMISSION' THEN amount ELSE 0 END) AS admission_amt,
                    SUM(amount) AS row_total
                FROM payments
                WHERE YEAR(paid_on) = ?
                GROUP BY MONTH(paid_on)
                ORDER BY MONTH(paid_on)
            ", [$year])->getResultArray();

            $monthNames = ['', 'January', 'February', 'March', 'April', 'May', 'June',
                           'July', 'August', 'September', 'October', 'November', 'December'];

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="collection_report_' . $year . '.csv"');
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Month', 'Payments', 'Monthly Fees (₹)', 'Admission Fees (₹)', 'Total (₹)']);
            $grandTotal = 0;
            $byMonth = [];
            foreach ($rows as $r) { $byMonth[(int) $r['mon']] = $r; }
            for ($m = 1; $m <= 12; $m++) {
                $r = $byMonth[$m] ?? ['payment_count' => 0, 'monthly_amt' => 0, 'admission_amt' => 0, 'row_total' => 0];
                fputcsv($out, [$monthNames[$m], $r['payment_count'], $r['monthly_amt'], $r['admission_amt'], $r['row_total']]);
                $grandTotal += (int) $r['row_total'];
            }
            fputcsv($out, ['TOTAL', '', '', '', $grandTotal]);
            fclose($out);
            exit;
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
            return redirect()->back()->with('error', 'Export failed.');
        }
    }

    private function monthsBetweenInclusive(string $fromMonth, string $toMonth): int
    {
        [$fy, $fm] = array_map('intval', explode('-', $fromMonth));
        [$ty, $tm] = array_map('intval', explode('-', $toMonth));
        $diff = ($ty * 12 + $tm) - ($fy * 12 + $fm);
        return max(0, $diff) + 1;
    }
}
