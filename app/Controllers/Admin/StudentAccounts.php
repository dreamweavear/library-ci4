<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StudentAccountModel;
use App\Models\StudentModel;

class StudentAccounts extends BaseController
{
    public function index()
    {
        $studentAccountModel = new StudentAccountModel();

        $accounts = $studentAccountModel
            ->select('student_accounts.*, students.full_name, students.phone')
            ->join('students', 'students.id = student_accounts.student_id')
            ->orderBy('student_accounts.id', 'DESC')
            ->findAll();

        return view('admin/student_accounts/index', [
            'title' => 'Student Accounts',
            'accounts' => $accounts,
        ]);
    }

    public function new()
    {
        $studentModel = new StudentModel();

        if ($this->request->getMethod(true) === 'POST') {
            $rules = [
                'student_id' => 'required|is_natural_no_zero',
                'username' => 'required|min_length[3]|max_length[60]',
                'password' => 'required|min_length[4]|max_length[100]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $studentId = (int) $this->request->getPost('student_id');
            $student = $studentModel->find($studentId);
            if (! $student) {
                return redirect()->back()->withInput()->with('error', 'Student not found.');
            }

            $username = strtolower(trim((string) $this->request->getPost('username')));
            $password = (string) $this->request->getPost('password');

            $accountModel = new StudentAccountModel();

            $existingUsername = $accountModel->where('username', $username)->first();
            if ($existingUsername) {
                return redirect()->back()->withInput()->with('error', 'Username already exists.');
            }

            $existingStudent = $accountModel->where('student_id', $studentId)->first();
            if ($existingStudent) {
                return redirect()->to(site_url('admin/student-accounts/' . (int) $existingStudent['id'] . '/reset'))->with('error', 'Account already exists. Reset password instead.');
            }

            $accountModel->insert([
                'student_id' => $studentId,
                'username' => $username,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'status' => 'ACTIVE',
            ]);

            return redirect()->to(site_url('admin/student-accounts'))->with('success', 'Student account created.');
        }

        $students = $studentModel->orderBy('id', 'DESC')->findAll(200);

        return view('admin/student_accounts/form', [
            'title' => 'Create Student Account',
            'students' => $students,
            'errors' => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function reset(int $id)
    {
        $accountModel = new StudentAccountModel();
        $account = $accountModel
            ->select('student_accounts.*, students.full_name, students.phone')
            ->join('students', 'students.id = student_accounts.student_id')
            ->where('student_accounts.id', $id)
            ->first();

        if (! $account) {
            return redirect()->to(site_url('admin/student-accounts'))->with('error', 'Account not found.');
        }

        if ($this->request->getMethod(true) === 'POST') {
            $rules = [
                'password' => 'required|min_length[4]|max_length[100]',
                'status' => 'required|in_list[ACTIVE,DISABLED]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $accountModel->update($id, [
                'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
                'status' => (string) $this->request->getPost('status'),
            ]);

            return redirect()->to(site_url('admin/student-accounts'))->with('success', 'Student account updated.');
        }

        return view('admin/student_accounts/reset', [
            'title' => 'Reset Student Password',
            'account' => $account,
            'errors' => session()->getFlashdata('errors') ?? [],
        ]);
    }
}
