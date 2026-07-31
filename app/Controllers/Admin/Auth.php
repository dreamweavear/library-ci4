<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminUserModel;

class Auth extends BaseController
{
    public function login()
    {
        try {
            $adminUserModel = new AdminUserModel();
            $hasAnyAdmin = $adminUserModel->countAllResults() > 0;

            if ($this->request->getMethod(true) === 'POST') {
                if (! $hasAnyAdmin) {
                    $rules = [
                        'name' => 'required|min_length[2]|max_length[120]',
                        'username' => 'required|min_length[3]|max_length[60]',
                        'password' => 'required|min_length[6]|max_length[100]',
                    ];

                    if (! $this->validate($rules)) {
                        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
                    }

                    $username = strtolower(trim((string) $this->request->getPost('username')));
                    $password = (string) $this->request->getPost('password');

                    $existing = $adminUserModel->where('username', $username)->first();
                    if ($existing) {
                        return redirect()->back()->withInput()->with('error', 'Username already exists.');
                    }

                    $adminId = $adminUserModel->insert([
                        'name' => trim((string) $this->request->getPost('name')),
                        'username' => $username,
                        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                        'role' => 'ADMIN',
                        'status' => 'ACTIVE',
                    ]);

                    session()->regenerate();
                    session()->set('admin_user_id', (int) $adminId);
                    session()->set('admin_username', $username);

                    return redirect()->to(site_url('admin'))->with('success', 'First admin created and logged in.');
                }

                $rules = [
                    'username' => 'required|min_length[3]|max_length[60]',
                    'password' => 'required|min_length[1]|max_length[100]',
                ];

                if (! $this->validate($rules)) {
                    return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
                }

                $username = strtolower(trim((string) $this->request->getPost('username')));
                $password = (string) $this->request->getPost('password');

                $admin = $adminUserModel->where('username', $username)->first();
                if (! $admin || ($admin['status'] ?? '') !== 'ACTIVE') {
                    return redirect()->back()->withInput()->with('error', 'Invalid username or account disabled.');
                }

                if (! password_verify($password, (string) ($admin['password_hash'] ?? ''))) {
                    return redirect()->back()->withInput()->with('error', 'Invalid password.');
                }

                $adminUserModel->update((int) $admin['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

                session()->regenerate();

                session()->set('admin_user_id', (int) $admin['id']);
                session()->set('admin_username', (string) $admin['username']);
                session()->set('admin_name', (string) $admin['name']);

                return redirect()->to(site_url('admin'))->with('success', 'Welcome back!');
            }

            if (! $hasAnyAdmin) {
                return view('admin/auth/first_admin', [
                    'title' => 'Create Admin · Brilient Brains Library',
                    'errors' => session()->getFlashdata('errors') ?? [],
                ]);
            }

            return view('admin/auth/login', [
                'title' => 'Admin Login · Brilient Brains Library',
                'errors' => session()->getFlashdata('errors') ?? [],
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Admin\\Auth::login exception: ' . $e);
            return view('site/setup_required', [
                'title' => 'Setup Required · Brilient Brains Library',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to(site_url('admin/login'))
        ->with('success', 'Logged out.');
        }
}
