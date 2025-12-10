<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class UserManagement extends Controller
{
    public function index()
    {
        $session = session();
        log_message('info', 'UserManagement::index() - Session data: ' . print_r($session->get(), true));
        if (! $session->get('isLoggedIn')) {
            log_message('info', 'UserManagement::index() - User not logged in');
            $session->setFlashdata('error', 'Please login first.');
            return redirect()->to('/login');
        }
        $role = strtolower((string) $session->get('role'));
        log_message('info', 'UserManagement::index() - User role: ' . $role);
        if ($role !== 'admin') {
            log_message('info', 'UserManagement::index() - Access denied for role: ' . $role);
            $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
            return redirect()->to('/dashboard');
        }

        $userModel = new UserModel();
        $users = $userModel->orderBy('id', 'ASC')->findAll();
        log_message('info', 'UserManagement::index() - Found ' . count($users) . ' users');

        return view('admin/users', ['users' => $users]);
    }

    public function updateRole()
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (! $session->get('isLoggedIn') || $role !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = (int) $this->request->getPost('user_id');
        $newRole = strtolower((string) $this->request->getPost('role'));

        $userModel = new UserModel();
        $ok = $userModel->updateRoleSafe($userId, $newRole);

        if ($ok) {
            return $this->response->setJSON(['success' => true, 'message' => 'Role updated successfully.']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Unable to update role. Protected or invalid user.']);
    }

    public function addUser()
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (! $session->get('isLoggedIn') || $role !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $name = trim((string) $this->request->getPost('name'));
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');
        $role = strtolower((string) $this->request->getPost('role'));

        // Basic server-side validation
        if ($name === '' || $email === '' || $password === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'All fields are required.']);
        }

        // Validate name: only letters, spaces, hyphens, apostrophes
        if (! preg_match("/^[a-zA-Z\s\-']+$/", $name)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Name can only contain letters, spaces, hyphens, and apostrophes.']);
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid email address.']);
        }

        if (! in_array($role, ['student','teacher','admin'], true)) {
            $role = 'student';
        }

        // Strong password: min 8, at least 1 number and 1 letter
        if (strlen($password) < 8 || ! preg_match('/[0-9]/', $password) || ! preg_match('/[A-Za-z]/', $password)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Password must be at least 8 characters and include letters and numbers.']);
        }

        $userModel = new UserModel();
        $existing = $userModel->where('email', $email)->first();
        if ($existing) {
            return $this->response->setJSON(['success' => false, 'message' => 'Email is already in use.']);
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            'email' => $email,
            'password' => $passwordHash,
            'role' => $role,
            'status' => 'active'
        ];

        $id = $userModel->createUser($data);

        if ($id) {
            return $this->response->setJSON(['success' => true, 'message' => 'User created successfully.']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to create user.']);
    }

    public function toggleStatus()
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (! $session->get('isLoggedIn') || $role !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = (int) $this->request->getPost('user_id');
        $status = $this->request->getPost('status') === 'inactive' ? 'inactive' : 'active';

        $userModel = new UserModel();
        $result = $userModel->setStatusSafe($userId, $status);

        if ($result) {
            return $this->response->setJSON(['success' => true, 'message' => 'Status updated.']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Unable to change status (protected or invalid).']);
    }

    public function editUser()
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (! $session->get('isLoggedIn') || $role !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = (int) $this->request->getPost('user_id');
        $name = trim((string) $this->request->getPost('name'));
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        // Basic server-side validation
        if ($name === '' || $email === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Name and email are required.']);
        }

        // Validate name: only letters, spaces, hyphens, apostrophes
        if (! preg_match("/^[a-zA-Z\s\-']+$/", $name)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Name can only contain letters, spaces, hyphens, and apostrophes.']);
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid email address.']);
        }

        // Check if email is already used by another user
        $userModel = new UserModel();
        $existing = $userModel->where('email', $email)->where('id !=', $userId)->first();
        if ($existing) {
            return $this->response->setJSON(['success' => false, 'message' => 'Email is already in use by another user.']);
        }

        $data = [
            'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            'email' => $email,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Only update password if provided
        if ($password !== '') {
            if (strlen($password) < 8 || ! preg_match('/[0-9]/', $password) || ! preg_match('/[A-Za-z]/', $password)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Password must be at least 8 characters and include letters and numbers.']);
            }
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $result = $userModel->update($userId, $data);

        if ($result) {
            return $this->response->setJSON(['success' => true, 'message' => 'User updated successfully.']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update user.']);
    }

    public function deleteUser()
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (! $session->get('isLoggedIn') || $role !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = (int) $this->request->getPost('user_id');

        $userModel = new UserModel();
        $user = $userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found.']);
        }

        if ($userModel->isProtectedAdmin($user)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot delete protected admin account.']);
        }

        $result = $userModel->update($userId, ['status' => 'deleted', 'updated_at' => date('Y-m-d H:i:s')]);

        if ($result) {
            return $this->response->setJSON(['success' => true, 'message' => 'User marked as deleted successfully.']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete user.']);
    }
}
