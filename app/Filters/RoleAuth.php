<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleAuth implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $uri = $request->getUri();
        $path = $uri->getPath();
        
        // Debug logging
        log_message('info', "RoleAuth Filter - Path: {$path}");
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            log_message('info', "RoleAuth Filter - User not logged in, redirecting to login");
            $session->setFlashdata('login_error', 'Please login to access this page.');
            return redirect()->to('/login');
        }
        
        $userRole = $session->get('role');
        
        // Debug logging
        log_message('info', "RoleAuth Filter - User Role: {$userRole}, Path: {$path}");
        
        // Check role-based access
        if ($userRole === 'admin') {
            // Admins can access any route starting with /admin
            if (strpos($path, '/admin') === 0) {
                log_message('info', "RoleAuth Filter - Admin access granted to: {$path}");
                return; // Allow access
            }
        } elseif ($userRole === 'teacher') {
            // Teachers can access routes starting with /teacher
            if (strpos($path, '/teacher') === 0) {
                log_message('info', "RoleAuth Filter - Teacher access granted to: {$path}");
                return; // Allow access
            }
        } elseif ($userRole === 'student') {
            // Students can access /student routes and /announcements
            if (strpos($path, '/student') === 0 || $path === '/announcements') {
                log_message('info', "RoleAuth Filter - Student access granted to: {$path}");
                return; // Allow access
            }
        }
        
        // Allow access to general routes for all logged-in users
        $allowedGeneralRoutes = ['/announcements', '/dashboard', '/logout', '/', '/about', '/contact'];
        if (in_array($path, $allowedGeneralRoutes)) {
            log_message('info', "RoleAuth Filter - General access granted to: {$path}");
            return; // Allow access
        }
        
        // If user tries to access a route not permitted for their role
        log_message('info', "RoleAuth Filter - Access denied for role {$userRole} to path {$path}");
        $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
        
        // Redirect based on user role to appropriate dashboard
        if ($userRole === 'admin') {
            return redirect()->to('/admin/dashboard');
        } elseif ($userRole === 'teacher') {
            return redirect()->to('/teacher/dashboard');
        } else {
            return redirect()->to('/dashboard');
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
