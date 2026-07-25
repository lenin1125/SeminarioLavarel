<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// IMPORTANTE: Aquí traemos los modelos y fachadas que usas en este código
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Support\Facades\DB;

class TiendaController extends Controller
{
    // Catálogo Principal
    public function index(Request $request)
    {
        // Carga las relaciones usando la configuración definida en el Modelo Producto
        $query = Producto::with(['categoria', 'tallas']);

        if ($request->filled('estilo')) {
            $query->where('categoria_id', $request->estilo);
        }

        if ($request->filled('precio_max')) {
            $query->where('precio', '<=', (float) $request->precio_max);
        }

        $productos = $query->orderBy('id', 'desc')->get();
        $zapatos = $productos; 

        $categorias = Categoria::all();
        $precioMaximoCatalogo = Producto::max('precio') ?? 1000000;

        return view('welcome', compact('productos', 'zapatos', 'categorias', 'precioMaximoCatalogo'));
    }

    // Ver Detalle del Zapato
    public function show($id)
    {
        $producto = Producto::with(['categoria', 'tallas'])->findOrFail($id);

        return view('zapatos.show', compact('producto'));
    }

    // Guardado Genérico por Tallas (Aunque esto debería ir al admin, lo dejamos aquí por ahora para no romperte nada)
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric',
        ]);

        $productoId = DB::table('productos')->insertGetId([
            'nombre'       => $request->nombre,
            'precio'       => $request->precio,
            'categoria_id' => $request->categoria_id,
            'genero'       => $request->genero ?? 'UNISEX',
            'imagen_url'   => $request->imagen_url,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        if ($request->has('tallas') && is_array($request->tallas)) {
            $columnasPivote = DB::getSchemaBuilder()->getColumnListing('producto_talla');
            $columnaStock = in_array('stock', $columnasPivote) ? 'stock' : 'cantidad';

            foreach ($request->tallas as $tallaId => $cantidad) {
                $cant = (int)$cantidad;
                if ($cant > 0) {
                    DB::table('producto_talla')->updateOrInsert(
                        ['producto_id' => $productoId, 'talla_id' => $tallaId],
                        [$columnaStock => $cant, 'updated_at' => now(), 'created_at' => now()]
                    );
                }
            }
        }

        return redirect()->back()->with('success', 'Producto y stock por tallas guardados correctamente.');
    }
}