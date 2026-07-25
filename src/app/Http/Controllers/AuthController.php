<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('tienda.index');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->route('tienda.index');
        }
        return back()->with('error', 'Credenciales incorrectas.');
    }

    public function showRegister()
    {
        if (Auth::check()) return redirect()->route('tienda.index');
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $partesNombre = explode(' ', trim($request->name), 2);
        $user = User::create([
            'nombre'   => $partesNombre[0],
            'apellido' => $partesNombre[1] ?? '.',
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'rol_id'   => 2,
        ]);
        
        Auth::login($user);
        return redirect()->route('tienda.index');
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('tienda.index');
    }
}