<?php
namespace App\Controllers;

use App\Controllers\BaseController;

class AuthController extends BaseController
{
    public function login()
    {
        return view('login');
    }

    public function doLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        // TODO: Vérifier les identifiants (exemple simple)
        if ($username === 'admin' && $password === 'admin') {
            session()->set('isLoggedIn', true);
            return redirect()->to('/');
        }
        return redirect()->back()->with('error', 'Identifiants invalides');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
