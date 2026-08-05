<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AlumniModel;
use App\Models\EnrollmentModel;
use App\Models\PaymentModel;
use App\Models\StudentAccountModel;
use App\Models\StudentModel;

class Students extends BaseController
{
    public function index()
    {
        try {
            $studentModel = new StudentModel();
            $q      = trim((string) $this->request->getGet('q'));
            $status = trim((string) $this->request->getGet('status'));

            if (! in_array($status, ['active', 'dormant', 'alumni'], true)) {
                $status = '';
            }

            $builder = $studentModel
                ->select(
                    "students.*,
                    (SELECT seats.seat_no FROM enrollments e JOIN seats ON seats.id = e.seat_id WHERE e.student_id = students.id AND e.status = 'ACTIVE' LIMIT 1) AS seat_no,
                    (SELECT seats.floor FROM enrollments e JOIN seats ON seats.id = e.seat_id WHERE e.student_id = students.id AND e.status = 'ACTIVE' LIMIT 1) AS seat_floor,
                    (SELECT IFNULL(SUM(p.amount), 0) FROM payments p WHERE p.student_id = students.id) AS fees_paid_total"
                )
                ->orderBy('id', 'DESC');

            if ($q !== '') {
                $builder = $builder
                    ->groupStart()
                    ->like('full_name', $q)
                    ->orLike('phone', $q)
                    ->groupEnd();
            }

            if ($status !== '') {
                $builder = $builder->where('students.status', $status);
            }

            $students = $builder->paginate(20);

            // Tab counts
            $db = \Config\Database::connect();
            $counts = [
                'all'     => (int) $db->table('students')->countAll(),
                'active'  => (int) $db->table('students')->where('status', 'active')->countAllResults(),
                'dormant' => (int) $db->table('students')->where('status', 'dormant')->countAllResults(),
                'alumni'  => (int) $db->table('students')->where('status', 'alumni')->countAllResults(),
            ];

            return view('admin/students/index', [
                'students' => $students,
                'pager'    => $studentModel->pager,
                'q'        => $q,
                'status'   => $status,
                'counts'   => $counts,
            ]);
        } catch (\Throwable $e) {
            return view('admin/setup', ['error' => $e->getMessage()]);
        }
    }

    public function show(int $id)
    {
        try {
            $studentModel    = new StudentModel();
            $enrollmentModel = new EnrollmentModel();
            $paymentModel    = new PaymentModel();

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

            $library  = config('Library');
            $payments = $paymentModel
                ->orderBy('id', 'DESC')
                ->where('student_id', $id)
                ->findAll();

            return view('admin/students/show', [
                'student'  => $student,
                'active'   => $active,
                'history'  => $history,
                'library'  => $library,
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
            'prefill' => null,
            'errors'  => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function create()
    {
        try {
            $rules = [
                'full_name'               => 'required|min_length[2]|max_length[120]',
                'phone'                   => 'permit_empty|max_length[20]',
                'guardian_name'           => 'permit_empty|max_length[120]',
                'notes'                   => 'permit_empty|max_length[2000]',
                'admission_date'          => 'permit_empty|valid_date[Y-m-d]',
                'admission_fee_collected' => 'permit_empty|is_natural',
                'dob'                     => 'permit_empty|valid_date[Y-m-d]',
                'aadhar_number'           => 'permit_empty|max_length[12]',
                'preparing_for'           => 'permit_empty|max_length[100]',
                'address'                 => 'permit_empty|max_length[2000]',
                'email'                   => 'permit_empty|valid_email|max_length[100]',
                'photo' => 'permit_empty|is_image[photo]|max_size[photo,2048]|mime_in[photo,image/jpg,image/jpeg,image/png,image/gif,image/webp]',
                ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $photoPath = null;
            $photoFile = $this->request->getFile('photo');
            if ($photoFile && $photoFile->isValid() && ! $photoFile->hasMoved()) {
                // 1. mime check
                $mime = $photoFile->getMimeType();

                //2. extension check
                $ext = strtolower($photoFile->getExtension());

                if (! in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                        return redirect()->back()
                            ->withInput()
                            ->with('errors', ['photo' => 'Invalid image extension.']);
                    }



                //mime check 
                if (! in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true)) {
                    return redirect()->back()->withInput()->with('errors', ['photo' => 'Photo must be an image (JPEG, PNG, GIF, WEBP).']);
                }
                //4-File size
                if ($photoFile->getSizeByUnit('mb') > 2) {
                    return redirect()->back()->withInput()->with('errors', ['photo' => 'Photo must be less than 2MB.']);
                }

                  // ⭐⭐⭐ YAHAN ADD KARNA HAI ⭐⭐⭐
                $imageInfo = getimagesize($photoFile->getTempName());

                if ($imageInfo === false) {
                    return redirect()->back()
                        ->withInput()
                        ->with('errors', ['photo' => 'Invalid image file.']);
                }

                [$width, $height] = $imageInfo;

                if ($width > 5000 || $height > 5000) {
                    return redirect()->back()
                        ->withInput()
                        ->with('errors', ['photo' => 'Image dimensions are too large.']);
                }


                //5 upload folder
                $uploadDir = FCPATH . 'uploads/students/';
                if (! is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Re-encode image via GD to strip any embedded payloads (polyglot/webshell defense)
                $imgType = $imageInfo[2];
                $gd      = null;
                if ($imgType === IMAGETYPE_JPEG)     { $gd = @imagecreatefromjpeg($photoFile->getTempName()); }
                elseif ($imgType === IMAGETYPE_PNG)  { $gd = @imagecreatefrompng($photoFile->getTempName()); }
                elseif ($imgType === IMAGETYPE_GIF)  { $gd = @imagecreatefromgif($photoFile->getTempName()); }
                elseif ($imgType === IMAGETYPE_WEBP) { $gd = @imagecreatefromwebp($photoFile->getTempName()); }

                if (! $gd) {
                    return redirect()->back()->withInput()->with('errors', ['photo' => 'Image processing failed. Please try another image.']);
                }

                // Flatten transparency (PNG/GIF alpha) onto white background before saving as JPEG
                $canvas = imagecreatetruecolor($width, $height);
                $white  = imagecolorallocate($canvas, 255, 255, 255);
                imagefill($canvas, 0, 0, $white);
                imagecopy($canvas, $gd, 0, 0, 0, 0, $width, $height);
                imagedestroy($gd);

                // Save as clean JPEG with cryptographically random filename
                $newName = bin2hex(random_bytes(16)) . '.jpg';
                imagejpeg($canvas, $uploadDir . $newName, 85);
                imagedestroy($canvas);

                $photoPath = 'uploads/students/' . $newName;
            }

            $studentModel = new StudentModel();
            $id = $studentModel->insert([
                'full_name'      => trim((string) $this->request->getPost('full_name')),
                'phone'          => trim((string) $this->request->getPost('phone')) ?: null,
                'guardian_name'  => trim((string) $this->request->getPost('guardian_name')) ?: null,
                'notes'          => trim((string) $this->request->getPost('notes')) ?: null,
                'admission_date' => trim((string) $this->request->getPost('admission_date')) ?: date('Y-m-d'),
                'photo'          => $photoPath,
                'aadhar_number'  => trim((string) $this->request->getPost('aadhar_number')) ?: null,
                'preparing_for'  => trim((string) $this->request->getPost('preparing_for')) ?: null,
                'address'        => trim((string) $this->request->getPost('address')) ?: null,
                'email'          => trim((string) $this->request->getPost('email')) ?: null,
                'dob'            => trim((string) $this->request->getPost('dob')) ?: null,
                'status'         => 'active',
            ]);

            $admissionFeeCollected = (int) ($this->request->getPost('admission_fee_collected') ?? 0);
            if ($admissionFeeCollected > 0) {
                $paymentModel = new PaymentModel();
                $receipt = $paymentModel->generateReceiptNo();
                $paymentModel->insert([
                    'student_id'    => (int) $id,
                    'enrollment_id' => null,
                    'type'          => 'ADMISSION',
                    'for_month'     => null,
                    'amount'        => $admissionFeeCollected,
                    'paid_on'        => trim((string) $this->request->getPost('admission_date')) ?: date('Y-m-d'),
                    'receipt_no'     => $receipt,
                    'receipt_number' => trim((string) $this->request->getPost('receipt_number')) ?: null,
                    'notes'          => 'Admission fee',
                ]);
            }

            $generatedCredentials = null;
            $phone = trim((string) $this->request->getPost('phone'));
            if ($phone !== '') {
                $accountModel   = new StudentAccountModel();
                $usernameExists = $accountModel->where('username', $phone)->first();
                $accountExists  = $accountModel->where('student_id', (int) $id)->first();
                if (! $usernameExists && ! $accountExists) {
                    $accountModel->insert([
                        'student_id'    => (int) $id,
                        'username'      => $phone,
                        'password_hash' => password_hash('student123', PASSWORD_DEFAULT),
                        'status'        => 'ACTIVE',
                    ]);
                    $generatedCredentials = ['username' => $phone, 'password' => 'student123'];
                }
            }

            if ($generatedCredentials !== null) {
                session()->setFlashdata('generated_login', $generatedCredentials);
            }

            return redirect()->to(site_url('admin/students/' . $id))
                ->with('success', 'Student created.' . ($generatedCredentials ? ' Login ID auto-generated.' : ''));
        } catch (\Throwable $e) {


           log_message('error', $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Unexpected error occurred. Please try again.');
                    
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
                'prefill' => null,
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
                'full_name'      => 'required|min_length[2]|max_length[120]',
                'phone'          => 'permit_empty|max_length[20]',
                'guardian_name'  => 'permit_empty|max_length[120]',
                'notes'          => 'permit_empty|max_length[2000]',
                'admission_date' => 'permit_empty|valid_date[Y-m-d]',
                'aadhar_number'  => 'permit_empty|max_length[12]',
                'dob'            => 'permit_empty|valid_date[Y-m-d]',
                'preparing_for'  => 'permit_empty|max_length[100]',
                'address'        => 'permit_empty|max_length[2000]',
                'email'          => 'permit_empty|valid_email|max_length[100]',
                'status'         => 'permit_empty|in_list[active,dormant,alumni]',
                'photo' => 'permit_empty|is_image[photo]|max_size[photo,2048]|mime_in[photo,image/jpeg,image/png,image/gif,image/webp]',
                ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $photoPath = $student['photo'] ?? null;
            $photoFile = $this->request->getFile('photo');
            if ($photoFile && $photoFile->isValid() && ! $photoFile->hasMoved()) {
                $mime = $photoFile->getMimeType();

                $ext = strtolower($photoFile->getExtension());

                if (! in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('errors', ['photo' => 'Invalid image extension.']);
                }



                if (! in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true)) {
                    return redirect()->back()->withInput()->with('errors', ['photo' => 'Photo must be an image (JPEG, PNG, GIF, WEBP).']);
                }
                if ($photoFile->getSizeByUnit('mb') > 2) {
                    return redirect()->back()->withInput()->with('errors', ['photo' => 'Photo must be less than 2MB.']);
                }

                $imageInfo = getimagesize($photoFile->getTempName());

                    if ($imageInfo === false) {
                        return redirect()->back()
                            ->withInput()
                            ->with('errors', ['photo' => 'Invalid image file.']);
                    }

                    [$width, $height] = $imageInfo;

                    if ($width > 5000 || $height > 5000) {
                        return redirect()->back()
                            ->withInput()
                            ->with('errors', ['photo' => 'Image dimensions are too large.']);
                    }




                $uploadDir = FCPATH . 'uploads/students/';
                if (! is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Re-encode image via GD to strip any embedded payloads (polyglot/webshell defense)
                $imgType = $imageInfo[2];
                $gd      = null;
                if ($imgType === IMAGETYPE_JPEG)     { $gd = @imagecreatefromjpeg($photoFile->getTempName()); }
                elseif ($imgType === IMAGETYPE_PNG)  { $gd = @imagecreatefrompng($photoFile->getTempName()); }
                elseif ($imgType === IMAGETYPE_GIF)  { $gd = @imagecreatefromgif($photoFile->getTempName()); }
                elseif ($imgType === IMAGETYPE_WEBP) { $gd = @imagecreatefromwebp($photoFile->getTempName()); }

                if (! $gd) {
                    return redirect()->back()->withInput()->with('errors', ['photo' => 'Image processing failed. Please try another image.']);
                }

                // Flatten transparency (PNG/GIF alpha) onto white background before saving as JPEG
                $canvas = imagecreatetruecolor($width, $height);
                $white  = imagecolorallocate($canvas, 255, 255, 255);
                imagefill($canvas, 0, 0, $white);
                imagecopy($canvas, $gd, 0, 0, 0, 0, $width, $height);
                imagedestroy($gd);

                // Delete old photo before saving new one
                if (! empty($student['photo']) && file_exists(FCPATH . $student['photo'])) {
                    unlink(FCPATH . $student['photo']);
                }

                // Save as clean JPEG with cryptographically random filename
                $newName = bin2hex(random_bytes(16)) . '.jpg';
                imagejpeg($canvas, $uploadDir . $newName, 85);
                imagedestroy($canvas);

                $photoPath = 'uploads/students/' . $newName;
            }

            $newStatus = trim((string) $this->request->getPost('status'));
            if (! in_array($newStatus, ['active', 'dormant', 'alumni'], true)) {
                $newStatus = $student['status'] ?? 'active';
            }

            // If editing status to alumni, copy to alumni table if not already there
            if ($newStatus === 'alumni' && ($student['status'] ?? '') !== 'alumni') {
                $alumniModel = new AlumniModel();
                if (! $alumniModel->where('student_id', $id)->first()) {
                    $alumniModel->insert([
                        'student_id'     => $id,
                        'full_name'      => $student['full_name'],
                        'phone'          => $student['phone'],
                        'guardian_name'  => $student['guardian_name'],
                        'email'          => $student['email'],
                        'address'        => $student['address'],
                        'preparing_for'  => $student['preparing_for'],
                        'photo'          => $student['photo'],
                        'admission_date' => $student['admission_date'],
                        'left_date'      => date('Y-m-d'),
                        'notes'          => $student['notes'],
                    ]);
                }
            }

            $studentModel->update($id, [
                'full_name'      => trim((string) $this->request->getPost('full_name')),
                'phone'          => trim((string) $this->request->getPost('phone')) ?: null,
                'guardian_name'  => trim((string) $this->request->getPost('guardian_name')) ?: null,
                'notes'          => trim((string) $this->request->getPost('notes')) ?: null,
                'admission_date' => trim((string) $this->request->getPost('admission_date')) ?: null,
                'photo'          => $photoPath,
                'aadhar_number'  => trim((string) $this->request->getPost('aadhar_number')) ?: null,
                'preparing_for'  => trim((string) $this->request->getPost('preparing_for')) ?: null,
                'address'        => trim((string) $this->request->getPost('address')) ?: null,
                'email'          => trim((string) $this->request->getPost('email')) ?: null,
                'dob'            => trim((string) $this->request->getPost('dob')) ?: null,
                'status'         => $newStatus,
            ]);

            return redirect()->to(site_url('admin/students/' . $id))->with('success', 'Student updated.');
        } catch (\Throwable $e) {

                log_message('error', $e->getMessage());

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Unexpected error occurred. Please try again.');
            }
    }




    /**
     * POST admin/students/(:num)/status
     * Quick status change from the student list.
     */
    public function changeStatus(int $id)
    {
        try {
            $newStatus = trim((string) $this->request->getPost('new_status'));
            if (! in_array($newStatus, ['active', 'dormant', 'alumni'], true)) {
                return redirect()->back()->with('error', 'Invalid status value.');
            }

            $studentModel = new StudentModel();
            $student = $studentModel->find($id);
            if (! $student) {
                return redirect()->to(site_url('admin/students'))->with('error', 'Student not found.');
            }

            if ($newStatus === 'alumni') {
                $alumniModel = new AlumniModel();
                if (! $alumniModel->where('student_id', $id)->first()) {
                    $alumniModel->insert([
                        'student_id'     => $id,
                        'full_name'      => $student['full_name'],
                        'phone'          => $student['phone'],
                        'guardian_name'  => $student['guardian_name'],
                        'email'          => $student['email'],
                        'address'        => $student['address'],
                        'preparing_for'  => $student['preparing_for'],
                        'photo'          => $student['photo'],
                        'admission_date' => $student['admission_date'],
                        'left_date'      => date('Y-m-d'),
                        'notes'          => $student['notes'],
                    ]);
                }
            }

            $studentModel->update($id, ['status' => $newStatus]);

            return redirect()->back()->with('success', esc($student['full_name']) . ' marked as ' . ucfirst($newStatus) . '.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * GET admin/students/from-alumni/(:num)
     * Pre-fill new admission form with alumni data.
     */
    public function fromAlumni(int $alumniId)
    {
        try {
            $alumniModel = new AlumniModel();
            $alumni = $alumniModel->find($alumniId);
            if (! $alumni) {
                return redirect()->to(site_url('admin/alumni'))->with('error', 'Alumni record not found.');
            }

            return view('admin/students/form', [
                'student' => null,
                'prefill' => $alumni,
                'errors'  => [],
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
