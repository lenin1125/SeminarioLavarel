<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ZapatoResource;
use App\Models\Zapato;
use Illuminate\Http\Request;

class ZapatoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Traemos todos los zapatos y los formateamos con nuestro Resource
        return ZapatoResource::collection(Zapato::orderBy('created_at', 'desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150|unique:zapatos,nombre',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'activo' => 'required|boolean',
        ]);

        $zapato = Zapato::create($validated);

        return response()->json([
            'mensaje' => '¡Zapato registrado con éxito vía API!',
            'datos' => new ZapatoResource($zapato)
        ], 201); // Estado 201: Creado
    }

    /**
     * Display the specified resource.
     */
    public function show(Zapato $zapato)
    {
        return new ZapatoResource($zapato);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Zapato $zapato)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150|unique:zapatos,nombre,' . $zapato->id,
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'activo' => 'required|boolean',
        ]);

        $zapato->update($validated);

        return response()->json([
            'mensaje' => '¡Zapato actualizado con éxito vía API!',
            'datos' => new ZapatoResource($zapato)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Zapato $zapato)
    {
        $zapato->delete();

        return response()->json([
            'mensaje' => 'El zapato ha sido eliminado correctamente del inventario.'
        ], 200);
    }
}