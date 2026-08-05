<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminUserModel;

class Auth extends BaseController
{
    private const MAX_ATTEMPTS   = 5;
    private const LOCKOUT_MINUTES = 15;

    // ── Helper: generate math captcha and store answer in session ────────────
    private function generateCaptcha(): array
    {
        $a  = random_int(2, 15);
        $b  = random_int(1, 10);
        $ops = ['+', '-', '×'];
        $op  = $ops[array_rand($ops)];

        $answer = match ($op) {
            '+'  => $a + $b,
            '-'  => $a - $b,
            '×'  => $a * $b,
        };

        session()->set('captcha_answer', $answer);
        return ['question' => "{$a} {$op} {$b} = ?", 'answer' => $answer];
    }

    // ── LOGIN ────────────────────────────────────────────────────────────────
    public function login()
    {
        try {
            $adminUserModel = new AdminUserModel();
            $hasAnyAdmin    = $adminUserModel->countAllResults() > 0;

            if ($this->request->getMethod(true) === 'POST') {

                // ── First-run: no admins exist yet ───────────────────────────
                if (! $hasAnyAdmin) {
                    $rules = [
                        'name'     => 'required|min_length[2]|max_length[120]',
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
                        'name'          => trim((string) $this->request->getPost('name')),
                        'username'      => $username,
                        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                        'role'          => 'ADMIN',
                        'status'        => 'ACTIVE',
                    ]);

                    session()->regenerate();
                    session()->set('admin_user_id', (int) $adminId);
                    session()->set('admin_username', $username);

                    return redirect()->to(site_url('admin'))->with('success', 'First admin created and logged in.');
                }

                // ── Normal login ──────────────────────────────────────────────

                // 1. Captcha check
                $userAnswer    = (int) $this->request->getPost('captcha_answer');
                $correctAnswer = (int) session()->get('captcha_answer');
                session()->remove('captcha_answer'); // one-time use

                if ($userAnswer !== $correctAnswer || $correctAnswer === 0) {
                    $captcha = $this->generateCaptcha();
                    return redirect()->back()->withInput()
                        ->with('error', 'Captcha answer is wrong. Please try again.')
                        ->with('captcha_question', $captcha['question']);
                }

                // 2. Basic validation
                $rules = [
                    'username' => 'required|min_length[3]|max_length[60]',
                    'password' => 'required|min_length[1]|max_length[100]',
                ];

                if (! $this->validate($rules)) {
                    $captcha = $this->generateCaptcha();
                    return redirect()->back()->withInput()
                        ->with('errors', $this->validator->getErrors())
                        ->with('captcha_question', $captcha['question']);
                }

                $username = strtolower(trim((string) $this->request->getPost('username')));
                $password = (string) $this->request->getPost('password');

                $admin = $adminUserModel->where('username', $username)->first();

                // 3. User not found
                if (! $admin || ($admin['status'] ?? '') !== 'ACTIVE') {
                    $captcha = $this->generateCaptcha();
                    return redirect()->back()->withInput()
                        ->with('error', 'Invalid username or account disabled.')
                        ->with('captcha_question', $captcha['question']);
                }

                // 4. Lockout check
                if (! empty($admin['locked_until']) && strtotime($admin['locked_until']) > time()) {
                    $remaining = ceil((strtotime($admin['locked_until']) - time()) / 60);
                    $captcha   = $this->generateCaptcha();
                    return redirect()->back()->withInput()
                        ->with('error', "Account locked. Try again in {$remaining} minute(s).")
                        ->with('captcha_question', $captcha['question']);
                }

                // 5. Password check
                if (! password_verify($password, (string) ($admin['password_hash'] ?? ''))) {
                    $attempts = (int) ($admin['failed_attempts'] ?? 0) + 1;
                    $update   = ['failed_attempts' => $attempts];

                    if ($attempts >= self::MAX_ATTEMPTS) {
                        $update['locked_until']    = date('Y-m-d H:i:s', time() + self::LOCKOUT_MINUTES * 60);
                        $update['failed_attempts'] = 0;
                    }

                    $adminUserModel->update((int) $admin['id'], $update);

                    $captcha = $this->generateCaptcha();
                    $left    = self::MAX_ATTEMPTS - $attempts;
                    $msg     = $attempts >= self::MAX_ATTEMPTS
                        ? 'Too many failed attempts. Account locked for ' . self::LOCKOUT_MINUTES . ' minutes.'
                        : "Invalid password. {$left} attempt(s) remaining before lockout.";

                    return redirect()->back()->withInput()
                        ->with('error', $msg)
                        ->with('captcha_question', $captcha['question']);
                }

                // 6. Success — reset counters
                $adminUserModel->update((int) $admin['id'], [
                    'last_login_at'  => date('Y-m-d H:i:s'),
                    'failed_attempts' => 0,
                    'locked_until'   => null,
                ]);

                session()->regenerate();
                session()->set('admin_user_id', (int) $admin['id']);
                session()->set('admin_username', (string) $admin['username']);
                session()->set('admin_name', (string) $admin['name']);

                return redirect()->to(site_url('admin'))->with('success', 'Welcome back!');
            }

            // ── GET: show login form ─────────────────────────────────────────
            if (! $hasAnyAdmin) {
                return view('admin/auth/first_admin', [
                    'title'  => 'Create Admin · Brilient Brains Library',
                    'errors' => session()->getFlashdata('errors') ?? [],
                ]);
            }

            $captcha = $this->generateCaptcha();
            return view('admin/auth/login', [
                'title'            => 'Admin Login · Brilient Brains Library',
                'errors'           => session()->getFlashdata('errors') ?? [],
                'captcha_question' => session()->getFlashdata('captcha_question') ?? $captcha['question'],
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'Admin\\Auth::login exception: ' . $e);
            return view('site/setup_required', [
                'title' => 'Setup Required · Brilient Brains Library',
                'error' => $e->getMessage(),
            ]);
        }
    }

    // ── CHANGE PASSWORD ──────────────────────────────────────────────────────
    public function changePassword()
    {
        $adminId = (int) session()->get('admin_user_id');
        if (! $adminId) {
            return redirect()->to(site_url('admin/login'));
        }

        $adminUserModel = new AdminUserModel();
        $admin          = $adminUserModel->find($adminId);
        if (! $admin) {
            return redirect()->to(site_url('admin/login'));
        }

        if ($this->request->getMethod(true) === 'POST') {
            $current = (string) $this->request->getPost('current_password');
            $new     = (string) $this->request->getPost('new_password');
            $confirm = (string) $this->request->getPost('confirm_password');

            if (! password_verify($current, (string) $admin['password_hash'])) {
                return redirect()->back()->with('error', 'Current password is incorrect.');
            }

            if (strlen($new) < 6) {
                return redirect()->back()->with('error', 'New password must be at least 6 characters.');
            }

            if ($new !== $confirm) {
                return redirect()->back()->with('error', 'New password and confirmation do not match.');
            }

            $adminUserModel->update($adminId, [
                'password_hash' => password_hash($new, PASSWORD_DEFAULT),
            ]);

            return redirect()->to(site_url('admin'))->with('success', 'Password changed successfully.');
        }

        return view('admin/auth/change_password', [
            'title' => 'Change Password · Admin',
        ]);
    }

    // ── LOGOUT ───────────────────────────────────────────────────────────────
    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('admin/login'))->with('success', 'Logged out.');
    }
}
