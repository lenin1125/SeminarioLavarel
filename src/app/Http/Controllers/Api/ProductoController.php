<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductoController extends Controller
{
    /**
     * Mostrar el catálogo completo de productos (zapatos)
     */
    public function index()
    {
        // Traemos los productos cargando la relación de su categoría para mostrarla
        $productos = Producto::with('categoria')->get();

        return response()->json([
            'success' => true,
            'data' => $productos
        ], 200);
    }

    /**
     * Registrar un nuevo zapato (Solo Administrador)
     */
    public function store(Request $request)
    {
        // 1. Validar datos
        $validator = Validator::make($request->all(), [
            'categoria_id' => 'required|exists:categorias,id', // Debe ser un ID de categoría válido
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'imagen_url' => 'nullable|string', // URL temporal o ruta de imagen
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Crear producto
        $producto = Producto::create([
            'categoria_id' => $request->categoria_id,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'imagen_url' => $request->imagen_url,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Producto registrado exitosamente.',
            'data' => $producto->load('categoria')
        ], 201);
    }

    /**
     * Mostrar un zapato específico
     */
    public function show($id)
    {
        $producto = Producto::with('categoria')->find($id);

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $producto
        ], 200);
    }
}