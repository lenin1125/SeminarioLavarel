<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PostsController extends Controller
{
    /**
     * Display a listing of external posts.
     */
    public function index()
    {
        // 1. Consumimos la API externa usando el Cliente HTTP de Laravel
        $response = Http::get('https://jsonplaceholder.typicode.com/posts');

        // 2. Inicializamos la variable de posts vacía
        $posts = [];

        // 3. Validamos si la conexión fue exitosa
        if ($response->successful()) {
            $posts = $response->json(); // Transformamos la respuesta directamente a un array
        } else {
            // Si la API externa falla, creamos un mensaje flash amigable
            session()->flash('error', 'No se pudo sincronizar la información con el servidor externo.');
        }

        // 4. Retornamos la vista enviando los datos limpios de los posts
        return view('posts.index', compact('posts'));
    }
}