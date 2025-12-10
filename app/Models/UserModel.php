<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = false; // timestamps handled by DB defaults in migration

    /**
     * Check if a user record corresponds to the protected admin.
     * Uses the PROTECTED_ADMIN_EMAIL constant.
     */
    public function isProtectedAdmin(array $user): bool
    {
        $protected = defined('PROTECTED_ADMIN_EMAIL') ? PROTECTED_ADMIN_EMAIL : null;
        if (!$protected) {
            return false;
        }

        return isset($user['email']) && strtolower($user['email']) === strtolower($protected);
    }

    /**
     * Safely update a user's role. Prevent changing the protected admin's role.
     * Returns true on success, false on failure.
     */
    public function updateRoleSafe(int $userId, string $newRole)
    {
        $user = $this->find($userId);
        if (!$user) {
            return false;
        }

        if ($this->isProtectedAdmin($user)) {
            return false; // protected admin role cannot be changed
        }

        $newRole = in_array($newRole, ['student', 'teacher', 'admin'], true) ? $newRole : $user['role'];
        return (bool) $this->update($userId, ['role' => $newRole, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Toggle active/inactive status for a user. Protected admin cannot be deactivated.
     */
    public function setStatusSafe(int $userId, string $status)
    {
        $user = $this->find($userId);
        if (!$user) {
            return false;
        }

        if ($this->isProtectedAdmin($user)) {
            return false; // cannot change protected admin's status
        }

        $status = $status === 'inactive' ? 'inactive' : 'active';
        return (bool) $this->update($userId, ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Create a user with validated data. Password must be pre-hashed by caller.
     * Returns the ID of the newly created user, or false on failure.
     */
    public function createUser(array $data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['status'] = $data['status'] ?? 'active';
        
        if ($this->insert($data)) {
            return $this->insertID();
        }
        return false;
    }

}


