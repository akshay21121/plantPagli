<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use App\Models\User;

class AuthController extends Controller {
    public function showLogin(): void {
        if (Auth::check()) {
            $this->redirect(Auth::isAdmin() ? '/admin' : '/account');
            return;
        }

        $this->render('account/login', [
            'title' => 'Sign In - PlantPuglu',
        ]);
    }

    public function login(): void {
        $this->validateCSRF();

        // Rate limiting check via session
        $loginAttempts = Session::get('_login_attempts', 0);
        $lastAttemptTime = Session::get('_last_login_time', 0);
        if ($loginAttempts >= 5 && (time() - $lastAttemptTime) < 300) {
            $remaining = 300 - (time() - $lastAttemptTime);
            $this->redirect('/login', 'error', "Too many failed attempts. Please wait {$remaining} seconds before trying again.");
            return;
        }

        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ];

        $validator = $this->validate($rules);
        if ($validator->fails()) {
            Session::set('_auth_errors', $validator->errors());
            $this->redirect('/login');
            return;
        }

        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (Auth::attempt($email, $password)) {
            // Reset rate limiter
            Session::remove('_login_attempts');
            Session::remove('_last_login_time');

            $user = Auth::user();
            if ($user['role'] === 'admin') {
                $this->redirect('/admin', 'success', 'Welcome to PlantPuglu Admin Control Panel!');
            } else {
                $this->redirect('/account', 'success', "Welcome back, {$user['name']}!");
            }
        } else {
            Session::set('_login_attempts', $loginAttempts + 1);
            Session::set('_last_login_time', time());
            $this->redirect('/login', 'error', 'Invalid email address or password.');
        }
    }

    public function showRegister(): void {
        if (Auth::check()) {
            $this->redirect('/account');
            return;
        }

        $this->render('account/register', [
            'title' => 'Create Account - PlantPuglu',
        ]);
    }

    public function register(): void {
        $this->validateCSRF();

        $rules = [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|phone',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|match:password',
        ];

        $validator = $this->validate($rules);
        if ($validator->fails()) {
            Session::set('_auth_errors', $validator->errors());
            $this->redirect('/register');
            return;
        }

        $userModel = new User();
        $userId = $userModel->create([
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'password' => $_POST['password'],
            'role' => 'customer'
        ]);

        $user = $userModel->findById($userId);
        Auth::login($user);

        $this->redirect('/account', 'success', 'Your PlantPuglu account has been created successfully!');
    }

    public function logout(): void {
        Auth::logout();
        $this->redirect('/login', 'info', 'You have been safely signed out.');
    }

    public function showForgotPassword(): void {
        $this->render('account/forgot_password', [
            'title' => 'Reset Password - PlantPuglu',
        ]);
    }

    public function forgotPassword(): void {
        $this->validateCSRF();

        $email = trim($_POST['email'] ?? '');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect('/forgot-password', 'error', 'Please enter a valid email address.');
            return;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        // Always show friendly message to prevent email enumeration attacks
        $this->redirect(
            '/login',
            'success',
            'If an account exists with this email, password reset instructions have been dispatched.'
        );
    }
}
