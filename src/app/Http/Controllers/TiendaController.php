<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        // Obtener Precio Mínimo y Precio Máximo real de todo el inventario
        $precioMinimoCatalogo = Producto::min('precio') ?? 0;
        $precioMaximoCatalogo = Producto::max('precio') ?? 500000;

        if ($request->filled('precio_max')) {
            $query->where('precio', '<=', (float) $request->precio_max);
        }

        // Paginación de 15 productos por página conservando los filtros en la URL
        $productos = $query->orderBy('id', 'desc')
                           ->paginate(15)
                           ->appends($request->all());

        $zapatos = $productos; 

        $categorias = Categoria::all();

        return view('welcome', compact('productos', 'zapatos', 'categorias', 'precioMinimoCatalogo', 'precioMaximoCatalogo'));
    }

    // Ver Detalle del Zapato
    public function show($id)
    {
        $producto = Producto::with(['categoria', 'tallas'])->findOrFail($id);

        return view('admin.zapatos.show', compact('producto'));
    }

    // Guardado Genérico por Tallas
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