<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminUserModel;

class Users extends BaseController
{
    public function index()
    {
        $adminUserModel = new AdminUserModel();
        $users = $adminUserModel->orderBy('id', 'DESC')->findAll();

        return view('admin/users/index', [
            'title' => 'Admin Users',
            'users' => $users,
        ]);
    }

    public function new()
    {
        if ($this->request->getMethod(true) === 'POST') {
            $rules = [
                'name' => 'required|min_length[2]|max_length[120]',
                'username' => 'required|min_length[3]|max_length[60]',
                'password' => 'required|min_length[6]|max_length[100]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $adminUserModel = new AdminUserModel();
            $username = strtolower(trim((string) $this->request->getPost('username')));

            if ($adminUserModel->where('username', $username)->first()) {
                return redirect()->back()->withInput()->with('error', 'Username already exists.');
            }

            $adminUserModel->insert([
                'name' => trim((string) $this->request->getPost('name')),
                'username' => $username,
                'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
                'role' => 'ADMIN',
                'status' => 'ACTIVE',
            ]);

            return redirect()->to(site_url('admin/users'))->with('success', 'Admin user created.');
        }

        return view('admin/users/form', [
            'title' => 'New Admin User',
            'user' => null,
            'errors' => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function edit(int $id)
    {
        $adminUserModel = new AdminUserModel();
        $user = $adminUserModel->find($id);
        if (! $user) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Admin user not found.');
        }

        if ($this->request->getMethod(true) === 'POST') {
            $rules = [
                'name' => 'required|min_length[2]|max_length[120]',
                'username' => 'required|min_length[3]|max_length[60]',
                'status' => 'required|in_list[ACTIVE,DISABLED]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $username = strtolower(trim((string) $this->request->getPost('username')));
            $existing = $adminUserModel->where('username', $username)->first();
            if ($existing && (int) $existing['id'] !== $id) {
                return redirect()->back()->withInput()->with('error', 'Username already exists.');
            }

            $adminUserModel->update($id, [
                'name' => trim((string) $this->request->getPost('name')),
                'username' => $username,
                'status' => (string) $this->request->getPost('status'),
            ]);

            return redirect()->to(site_url('admin/users'))->with('success', 'Admin user updated.');
        }

        return view('admin/users/form', [
            'title' => 'Edit Admin User',
            'user' => $user,
            'errors' => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function reset(int $id)
    {
        $adminUserModel = new AdminUserModel();
        $user = $adminUserModel->find($id);
        if (! $user) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Admin user not found.');
        }

        if ($this->request->getMethod(true) === 'POST') {
            $rules = [
                'password' => 'required|min_length[6]|max_length[100]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $adminUserModel->update($id, [
                'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            ]);

            return redirect()->to(site_url('admin/users'))->with('success', 'Password updated.');
        }

        return view('admin/users/reset', [
            'title' => 'Reset Admin Password',
            'user' => $user,
            'errors' => session()->getFlashdata('errors') ?? [],
        ]);
    }
}
