<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\StudentAccountModel;
use App\Models\StudentModel;

class Auth extends BaseController
{
    public function login()
    {
        // If tables are not migrated yet, show a setup page.
        try {
            $testModel = new StudentAccountModel();
            $testModel->countAllResults();
        } catch (\Throwable $e) {
            return view('site/setup_required', [
                'title' => 'Setup Required · Brilient Brains Library',
                'error' => $e->getMessage(),
            ]);
        }

        if ($this->request->getMethod(true) === 'POST') {
            $rules = [
                'username' => 'required|min_length[3]|max_length[60]',
                'password' => 'required|min_length[1]|max_length[100]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $username = strtolower(trim((string) $this->request->getPost('username')));
            $password = (string) $this->request->getPost('password');

            $accountModel = new StudentAccountModel();
            $account = $accountModel->where('username', $username)->first();

            if (! $account || ($account['status'] ?? '') !== 'ACTIVE') {
                return redirect()->back()->withInput()->with('error', 'Invalid username or account disabled.');
            }

            if (! password_verify($password, (string) ($account['password_hash'] ?? ''))) {
                return redirect()->back()->withInput()->with('error', 'Invalid password.');
            }

            $accountModel->update((int) $account['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

            $studentModel = new StudentModel();
            $student = $studentModel->find((int) $account['student_id']);

            session()->set('student_account_id', (int) $account['id']);
            session()->set('student_id', (int) $account['student_id']);
            session()->set('student_name', (string) ($student['full_name'] ?? 'Student'));

            return redirect()->to(site_url('student'))->with('success', 'Welcome!');
        }

        return view('site/student_login', [
            'title' => 'Student Login · Brilient Brains Library',
            'errors' => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function logout()
    {
        session()->remove(['student_account_id', 'student_id', 'student_name']);
        return redirect()->to(site_url('student/login'))->with('success', 'Logged out.');
    }
}
