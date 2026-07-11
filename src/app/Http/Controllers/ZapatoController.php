<?php

namespace App\Http\Controllers;

use App\Models\Zapato;
use Illuminate\Http\Request;

class ZapatoController extends Controller
{
    /**
     * Bloques 2, 3, 4 y 6: Listado, Eager Loading, Scopes y Estadísticas
     */
    public function index(Request $request)
    {
        // Bloque 3: Eager Loading para evitar el problema N+1 (Optimización)
        $query = Zapato::with(['user']);

        // Bloque 4: Aplicación de Scopes Locales (Filtros reutilizables)
        if ($request->filled('estado')) {
            if ($request->estado == 'activo') {
                $query->activos();
            } else {
                $query->inactivos();
            }
        }

        if ($request->filled('buscar')) {
            $query->buscar($request->buscar);
        }

        $zapatos = $query->get();

        // Retorna la vista con los datos filtrados
        return view('zapatos.index', compact('zapatos'));
    }

    /**
     * Bloque 5: Formulario de creación
     */
    public function create()
    {
        return view('zapatos.create');
    }

    /**
     * Bloque 5: Guardar el registro con validación y CSRF
     */
    public function store(Request $request)
    {
        // Validación estricta de campos
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'activo' => 'required|boolean',
        ]);

        $data = $request->all();
        // Mock de usuario si no se está logueado (asigna el ID 1 por defecto)
        $data['user_id'] = auth()->id() ?? 1; 

        Zapato::create($data);

        return redirect()->route('zapatos.index')->with('success', '¡Zapato creado correctamente!');
    }

    /**
     * Mostrar un registro específico (Opcional en web, comúnmente redirige a index)
     */
    public function show(Zapato $zapato)
    {
        return view('zapatos.show', compact('zapato'));
    }

    /**
     * Bloque 7: Formulario de edición
     */
    public function edit(Zapato $zapato)
    {
        return view('zapatos.edit', compact('zapato'));
    }

    /**
     * Bloque 7: Actualizar el registro
     */
    public function update(Request $request, Zapato $zapato)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'activo' => 'required|boolean',
        ]);

        $zapato->update($request->all());

        return redirect()->route('zapatos.index')->with('success', '¡Zapato actualizado con éxito!');
    }

    /**
     * Bloque 7: Eliminar el registro de la Base de Datos
     */
    public function destroy(Zapato $zapato)
    {
        $zapato->delete();

        return redirect()->route('zapatos.index')->with('success', '¡Zapato eliminado de forma permanente!');
    }
}