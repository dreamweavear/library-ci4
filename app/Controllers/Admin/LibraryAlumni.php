<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AlumniModel;
use App\Models\StudentModel;

class LibraryAlumni extends BaseController
{
    /**
     * GET admin/alumni
     * List alumni with search.
     */
    public function index()
    {
        try {
            $alumniModel = new AlumniModel();
            $q = trim((string) $this->request->getGet('q'));

            $builder = $alumniModel->orderBy('id', 'DESC');
            if ($q !== '') {
                $builder = $builder
                    ->groupStart()
                    ->like('full_name', $q)
                    ->orLike('phone', $q)
                    ->groupEnd();
            }

            $alumni = $builder->paginate(25);
            $total  = $alumniModel->countAll();

            return view('admin/alumni/index', [
                'alumni' => $alumni,
                'pager'  => $alumniModel->pager,
                'q'      => $q,
                'total'  => $total,
            ]);
        } catch (\Throwable $e) {
            return view('admin/setup', ['error' => $e->getMessage()]);
        }
    }

    /**
     * GET admin/alumni/new
     */
    public function new()
    {
        return view('admin/alumni/new', [
            'errors' => session()->getFlashdata('errors') ?? [],
        ]);
    }

    /**
     * POST admin/alumni/create
     */
    public function create()
    {
        try {
            $rules = [
                'full_name'      => 'required|min_length[2]|max_length[120]',
                'phone'          => 'permit_empty|max_length[20]',
                'dob'            => 'permit_empty|valid_date[Y-m-d]',
                'guardian_name'  => 'permit_empty|max_length[120]',
                'email'          => 'permit_empty|valid_email|max_length[100]',
                'preparing_for'  => 'permit_empty|max_length[100]',
                'admission_date' => 'permit_empty|valid_date[Y-m-d]',
                'left_date'      => 'permit_empty|valid_date[Y-m-d]',
                'notes'          => 'permit_empty|max_length[2000]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $alumniModel = new AlumniModel();
            $alumniModel->insert([
                'student_id'     => null,
                'full_name'      => trim((string) $this->request->getPost('full_name')),
                'phone'          => trim((string) $this->request->getPost('phone')) ?: null,
                'dob'            => trim((string) $this->request->getPost('dob')) ?: null,
                'guardian_name'  => trim((string) $this->request->getPost('guardian_name')) ?: null,
                'email'          => trim((string) $this->request->getPost('email')) ?: null,
                'address'        => trim((string) $this->request->getPost('address')) ?: null,
                'preparing_for'  => trim((string) $this->request->getPost('preparing_for')) ?: null,
                'admission_date' => trim((string) $this->request->getPost('admission_date')) ?: null,
                'left_date'      => trim((string) $this->request->getPost('left_date')) ?: null,
                'notes'          => trim((string) $this->request->getPost('notes')) ?: null,
            ]);

            return redirect()->to(site_url('admin/alumni'))->with('success', 'Alumni record added.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * GET admin/alumni/(:num)/edit
     */
    public function edit(int $id)
    {
        try {
            $alumniModel = new AlumniModel();
            $alumni = $alumniModel->find($id);
            if (! $alumni) {
                return redirect()->to(site_url('admin/alumni'))->with('error', 'Alumni not found.');
            }

            return view('admin/alumni/edit', [
                'alumni' => $alumni,
                'errors' => session()->getFlashdata('errors') ?? [],
            ]);
        } catch (\Throwable $e) {
            return view('admin/setup', ['error' => $e->getMessage()]);
        }
    }

    /**
     * POST admin/alumni/(:num)/update
     */
    public function update(int $id)
    {
        try {
            $alumniModel = new AlumniModel();
            $alumni = $alumniModel->find($id);
            if (! $alumni) {
                return redirect()->to(site_url('admin/alumni'))->with('error', 'Alumni not found.');
            }

            $rules = [
                'full_name'      => 'required|min_length[2]|max_length[120]',
                'phone'          => 'permit_empty|max_length[20]',
                'dob'            => 'permit_empty|valid_date[Y-m-d]',
                'guardian_name'  => 'permit_empty|max_length[120]',
                'email'          => 'permit_empty|valid_email|max_length[100]',
                'preparing_for'  => 'permit_empty|max_length[100]',
                'admission_date' => 'permit_empty|valid_date[Y-m-d]',
                'left_date'      => 'permit_empty|valid_date[Y-m-d]',
                'notes'          => 'permit_empty|max_length[2000]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $alumniModel->update($id, [
                'full_name'      => trim((string) $this->request->getPost('full_name')),
                'phone'          => trim((string) $this->request->getPost('phone')) ?: null,
                'dob'            => trim((string) $this->request->getPost('dob')) ?: null,
                'guardian_name'  => trim((string) $this->request->getPost('guardian_name')) ?: null,
                'email'          => trim((string) $this->request->getPost('email')) ?: null,
                'address'        => trim((string) $this->request->getPost('address')) ?: null,
                'preparing_for'  => trim((string) $this->request->getPost('preparing_for')) ?: null,
                'admission_date' => trim((string) $this->request->getPost('admission_date')) ?: null,
                'left_date'      => trim((string) $this->request->getPost('left_date')) ?: null,
                'notes'          => trim((string) $this->request->getPost('notes')) ?: null,
            ]);

            return redirect()->to(site_url('admin/alumni'))->with('success', 'Alumni record updated.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * GET admin/alumni/import
     * Show CSV import form.
     */
    public function importCsv()
    {
        return view('admin/alumni/import', [
            'result' => session()->getFlashdata('import_result'),
            'errors' => session()->getFlashdata('errors') ?? [],
        ]);
    }

    /**
     * POST admin/alumni/import
     * Process uploaded CSV — batch insert, duplicate check by phone.
     */
    public function importStore()
    {
        try {
            $file = $this->request->getFile('csv_file');
            if (! $file || ! $file->isValid()) {
                return redirect()->back()->with('errors', ['csv_file' => 'Please upload a valid CSV file.']);
            }
            if (strtolower($file->getExtension()) !== 'csv') {
                return redirect()->back()->with('errors', ['csv_file' => 'File must be a .csv file.']);
            }

            $onDuplicate = $this->request->getPost('on_duplicate') === 'update' ? 'update' : 'skip';

            @set_time_limit(300);

            $handle = fopen($file->getTempName(), 'r');
            if (! $handle) {
                return redirect()->back()->with('errors', ['csv_file' => 'Could not read the uploaded file.']);
            }

            // Read header row
            $header = fgetcsv($handle);
            if (! $header) {
                fclose($handle);
                return redirect()->back()->with('errors', ['csv_file' => 'CSV file is empty or unreadable.']);
            }

            // Normalize header keys
            $header = array_map(fn($h) => strtolower(trim((string) $h)), $header);

            $alumniModel = new AlumniModel();
            $batch       = [];
            $imported    = 0;
            $skipped     = 0;
            $updated     = 0;
            $batchSize   = 50;

            $now = date('Y-m-d H:i:s');

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 2) {
                    continue;
                }
                $data = array_combine($header, array_pad($row, count($header), ''));
                if ($data === false) {
                    continue;
                }

                $fullName = trim((string) ($data['full_name'] ?? ''));
                if ($fullName === '') {
                    $skipped++;
                    continue;
                }

                $phone = trim((string) ($data['phone'] ?? '')) ?: null;

                // Duplicate check by phone
                if ($phone !== null) {
                    $existing = $alumniModel->where('phone', $phone)->first();
                    if ($existing) {
                        if ($onDuplicate === 'update') {
                            $alumniModel->update($existing['id'], [
                                'full_name'      => $fullName,
                                'dob'            => trim((string) ($data['dob'] ?? '')) ?: null,
                                'guardian_name'  => trim((string) ($data['guardian_name'] ?? '')) ?: null,
                                'email'          => trim((string) ($data['email'] ?? '')) ?: null,
                                'address'        => trim((string) ($data['address'] ?? '')) ?: null,
                                'preparing_for'  => trim((string) ($data['preparing_for'] ?? '')) ?: null,
                                'admission_date' => trim((string) ($data['admission_date'] ?? '')) ?: null,
                                'left_date'      => trim((string) ($data['left_date'] ?? '')) ?: null,
                                'notes'          => trim((string) ($data['notes'] ?? '')) ?: null,
                            ]);
                            $updated++;
                        } else {
                            $skipped++;
                        }
                        continue;
                    }
                }

                $batch[] = [
                    'student_id'     => null,
                    'full_name'      => $fullName,
                    'phone'          => $phone,
                    'dob'            => trim((string) ($data['dob'] ?? '')) ?: null,
                    'guardian_name'  => trim((string) ($data['guardian_name'] ?? '')) ?: null,
                    'email'          => trim((string) ($data['email'] ?? '')) ?: null,
                    'address'        => trim((string) ($data['address'] ?? '')) ?: null,
                    'preparing_for'  => trim((string) ($data['preparing_for'] ?? '')) ?: null,
                    'admission_date' => trim((string) ($data['admission_date'] ?? '')) ?: null,
                    'left_date'      => trim((string) ($data['left_date'] ?? '')) ?: null,
                    'notes'          => trim((string) ($data['notes'] ?? '')) ?: null,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];

                if (count($batch) >= $batchSize) {
                    $alumniModel->insertBatch($batch);
                    $imported += count($batch);
                    $batch = [];
                }
            }

            fclose($handle);

            // Insert remaining
            if (! empty($batch)) {
                $alumniModel->insertBatch($batch);
                $imported += count($batch);
            }

            $msg = "Import complete: {$imported} imported, {$updated} updated, {$skipped} skipped.";
            session()->setFlashdata('import_result', $msg);

            return redirect()->to(site_url('admin/alumni/import'))->with('success', $msg);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * GET admin/alumni/export
     * Download all alumni as CSV.
     */
    public function exportCsv()
    {
        try {
            $alumniModel = new AlumniModel();
            $all = $alumniModel->orderBy('id', 'DESC')->findAll();

            $filename = 'alumni_export_' . date('Y-m-d') . '.csv';

            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');

            $out = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'id', 'student_id', 'full_name', 'phone', 'dob', 'guardian_name',
                'email', 'address', 'preparing_for',
                'admission_date', 'left_date', 'notes',
            ]);

            foreach ($all as $row) {
                fputcsv($out, [
                    $row['id'],
                    $row['student_id'] ?? '',
                    $row['full_name'],
                    $row['phone'] ?? '',
                    $row['dob'] ?? '',
                    $row['guardian_name'] ?? '',
                    $row['email'] ?? '',
                    $row['address'] ?? '',
                    $row['preparing_for'] ?? '',
                    $row['admission_date'] ?? '',
                    $row['left_date'] ?? '',
                    $row['notes'] ?? '',
                ]);
            }

            fclose($out);
            exit;
        } catch (\Throwable $e) {
            return redirect()->to(site_url('admin/alumni'))->with('error', $e->getMessage());
        }
    }

    /**
     * POST admin/alumni/(:num)/readmit
     * Re-admit alumni — reactivates existing student or creates new one.
     */
    public function readmit(int $id)
    {
        try {
            $alumniModel = new AlumniModel();
            $alumni = $alumniModel->find($id);
            if (! $alumni) {
                return redirect()->to(site_url('admin/alumni'))->with('error', 'Alumni not found.');
            }

            $studentModel = new StudentModel();

            // If linked to a student record, reactivate it
            if (! empty($alumni['student_id'])) {
                $student = $studentModel->find((int) $alumni['student_id']);
                if ($student) {
                    $studentModel->update((int) $alumni['student_id'], [
                        'status' => 'active',
                    ]);
                    return redirect()->to(site_url('admin/students/' . $alumni['student_id']))
                        ->with('success', $alumni['full_name'] . ' re-admitted (existing record reactivated).');
                }
            }

            // No linked student — redirect to pre-filled admission form
            return redirect()->to(site_url('admin/students/from-alumni/' . $id))
                ->with('success', 'Fill in the admission form to re-admit this alumni.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
