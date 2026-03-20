<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\PaymentModel;
use App\Models\StudentModel;

class Students extends BaseController
{
    public function index()
    {
        try {
            $studentModel = new StudentModel();
            $q = trim((string) $this->request->getGet('q'));

        $builder = $studentModel
            ->select(
                "students.*,
                (SELECT seats.seat_no FROM enrollments e JOIN seats ON seats.id = e.seat_id WHERE e.student_id = students.id AND e.status = 'ACTIVE' LIMIT 1) AS seat_no,
                (SELECT seats.floor FROM enrollments e JOIN seats ON seats.id = e.seat_id WHERE e.student_id = students.id AND e.status = 'ACTIVE' LIMIT 1) AS seat_floor,
                (SELECT e.start_date FROM enrollments e WHERE e.student_id = students.id AND e.status = 'ACTIVE' LIMIT 1) AS seat_start_date,
                (SELECT IFNULL(SUM(p.amount), 0) FROM payments p WHERE p.student_id = students.id) AS fees_paid_total,
                (SELECT IFNULL(SUM(p.amount), 0) FROM payments p WHERE p.student_id = students.id AND p.type = 'MONTHLY') AS fees_paid_monthly"
            )
            ->orderBy('id', 'DESC');
        if ($q !== '') {
            $builder = $builder
                ->groupStart()
                ->like('full_name', $q)
                ->orLike('phone', $q)
                ->groupEnd();
        }

            $students = $builder->paginate(20);

            return view('admin/students/index', [
                'students' => $students,
                'pager'    => $studentModel->pager,
                'q'        => $q,
            ]);
        } catch (\Throwable $e) {
            return view('admin/setup', ['error' => $e->getMessage()]);
        }
    }

    public function show(int $id)
    {
        try {
            $studentModel = new StudentModel();
            $enrollmentModel = new EnrollmentModel();
            $paymentModel = new PaymentModel();

        $student = $studentModel->find($id);
        if (! $student) {
            return redirect()->to(site_url('admin/students'))->with('error', 'Student not found.');
        }

        $active = $enrollmentModel
            ->select('enrollments.*, seats.seat_no, seats.floor')
            ->join('seats', 'seats.id = enrollments.seat_id')
            ->where('enrollments.student_id', $id)
            ->where('enrollments.status', 'ACTIVE')
            ->first();

        $history = $enrollmentModel
            ->select('enrollments.*, seats.seat_no, seats.floor')
            ->join('seats', 'seats.id = enrollments.seat_id')
            ->where('enrollments.student_id', $id)
            ->orderBy('enrollments.id', 'DESC')
            ->findAll();

        $library = config('Library');

        $payments = $paymentModel
            ->orderBy('id', 'DESC')
            ->where('student_id', $id)
            ->findAll();

            return view('admin/students/show', [
                'student' => $student,
                'active'  => $active,
                'history' => $history,
                'library' => $library,
                'payments' => $payments,
            ]);
        } catch (\Throwable $e) {
            return view('admin/setup', ['error' => $e->getMessage()]);
        }
    }

    public function new()
    {
        return view('admin/students/form', [
            'student' => null,
            'errors'  => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function create()
    {
        try {
        $rules = [
            'full_name' => 'required|min_length[2]|max_length[120]',
            'phone'     => 'permit_empty|max_length[20]',
            'guardian_name' => 'permit_empty|max_length[120]',
            'notes'     => 'permit_empty|max_length[2000]',
            'admission_date' => 'permit_empty|valid_date[Y-m-d]',
            'admission_fee_collected' => 'permit_empty|is_natural',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $studentModel = new StudentModel();
        $id = $studentModel->insert([
            'full_name'      => trim((string) $this->request->getPost('full_name')),
            'phone'          => trim((string) $this->request->getPost('phone')) ?: null,
            'guardian_name'  => trim((string) $this->request->getPost('guardian_name')) ?: null,
            'notes'          => trim((string) $this->request->getPost('notes')) ?: null,
            'admission_date' => trim((string) $this->request->getPost('admission_date')) ?: date('Y-m-d'),
        ]);

        $admissionFeeCollected = (int) ($this->request->getPost('admission_fee_collected') ?? 0);
        if ($admissionFeeCollected > 0) {
            $paymentModel = new PaymentModel();
            $receipt = $paymentModel->generateReceiptNo();
            $paymentModel->insert([
                'student_id' => (int) $id,
                'enrollment_id' => null,
                'type' => 'ADMISSION',
                'for_month' => null,
                'amount' => $admissionFeeCollected,
                'paid_on' => trim((string) $this->request->getPost('admission_date')) ?: date('Y-m-d'),
                'receipt_no' => $receipt,
                'notes' => 'Admission fee',
            ]);
        }

        return redirect()->to(site_url('admin/students/' . $id))->with('success', 'Student created.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(int $id)
    {
        try {
            $studentModel = new StudentModel();
            $student = $studentModel->find($id);
            if (! $student) {
                return redirect()->to(site_url('admin/students'))->with('error', 'Student not found.');
            }

            return view('admin/students/form', [
                'student' => $student,
                'errors'  => session()->getFlashdata('errors') ?? [],
            ]);
        } catch (\Throwable $e) {
            return view('admin/setup', ['error' => $e->getMessage()]);
        }
    }

    public function update(int $id)
    {
        try {
            $studentModel = new StudentModel();
            $student = $studentModel->find($id);
            if (! $student) {
                return redirect()->to(site_url('admin/students'))->with('error', 'Student not found.');
            }

        $rules = [
            'full_name' => 'required|min_length[2]|max_length[120]',
            'phone'     => 'permit_empty|max_length[20]',
            'guardian_name' => 'permit_empty|max_length[120]',
            'notes'     => 'permit_empty|max_length[2000]',
            'admission_date' => 'permit_empty|valid_date[Y-m-d]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $studentModel->update($id, [
            'full_name'      => trim((string) $this->request->getPost('full_name')),
            'phone'          => trim((string) $this->request->getPost('phone')) ?: null,
            'guardian_name'  => trim((string) $this->request->getPost('guardian_name')) ?: null,
            'notes'          => trim((string) $this->request->getPost('notes')) ?: null,
            'admission_date' => trim((string) $this->request->getPost('admission_date')) ?: null,
        ]);

            return redirect()->to(site_url('admin/students/' . $id))->with('success', 'Student updated.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
