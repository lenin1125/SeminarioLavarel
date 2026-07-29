<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class ZapatoController extends Controller
{
    /**
     * Listar inventario de zapatos con filtros de Categoría y Estado
     */
    public function index(Request $request)
    {
        $query = Producto::with(['categoria', 'tallas']);

        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        if ($request->filled('estado')) {
            $estado = strtolower($request->estado);
            
            if ($estado === 'disponible' || $estado === '1') {
                $query->where('activo', 1);
            } elseif (in_array($estado, ['agotado', 'deshabilitado', '0'])) {
                $query->where('activo', 0);
            }
        }

        $zapatos = $query->orderBy('id', 'desc')
                         ->paginate(15)
                         ->appends($request->all());

        $productos = $zapatos; // Alias de compatibilidad
        $categorias = Schema::hasTable('categorias') ? DB::table('categorias')->get() : collect([]);

        return view('admin.zapatos.index', compact('zapatos', 'productos', 'categorias'));
    }

    /**
     * Formulario para crear un nuevo zapato
     */
    public function create()
    {
        $categorias = Schema::hasTable('categorias') ? DB::table('categorias')->get() : collect([]);
        $tallas     = Schema::hasTable('tallas') ? DB::table('tallas')->get() : collect([]);

        return view('admin.zapatos.create', compact('categorias', 'tallas'));
    }

    /**
     * Guardar zapato en base de datos
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'imagen_principal' => 'nullable|image|max:5120',
            'imagen'           => 'nullable|image|max:5120',
        ]);

        // Detecta si la imagen viene como 'imagen_principal' o 'imagen'
        $fileKey = $request->hasFile('imagen_principal') ? 'imagen_principal' : ($request->hasFile('imagen') ? 'imagen' : null);
        $imagenUrl = null;

        if ($fileKey) {
            try {
                $file         = $request->file($fileKey);
                $cloudName    = env('CLOUDINARY_CLOUD_NAME', 'x5lp98vz');
                $uploadPreset = env('CLOUDINARY_UPLOAD_PRESET', 'sneakerslh_preset');

                $response = Http::timeout(30)->withoutVerifying()
                    ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                    ->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", [
                        'upload_preset' => $uploadPreset,
                    ]);

                if ($response->successful()) {
                    $imagenUrl = $response->json()['secure_url'] ?? null;
                }
            } catch (\Exception $e) {
                $imagenUrl = null;
            }

            if (!$imagenUrl) {
                $path = $request->file($fileKey)->store('productos', 'public');
                $imagenUrl = asset('storage/' . $path);
            }
        }

        // Datos principales del zapato
        $dataInsert = [
            'nombre'       => $request->nombre,
            'descripcion'  => $request->descripcion ?? '',
            'precio'       => $request->precio,
            'categoria_id' => $request->categoria_id ?? null,
            'imagen_url'   => $imagenUrl ?? 'https://placehold.co/400x400/1e293b/ffffff?text=Sin+Imagen',
            'activo'       => 1,
            'created_at'   => now(),
            'updated_at'   => now()
        ];

        // Añade genero si la columna existe en la tabla productos
        if (Schema::hasColumn('productos', 'genero') && $request->has('genero')) {
            $dataInsert['genero'] = $request->genero;
        }

        $productoId = DB::table('productos')->insertGetId($dataInsert);

        // Guardar stock e inventario de tallas en la tabla pivote
        if (Schema::hasTable('producto_talla')) {
            $columnasPivote = Schema::getColumnListing('producto_talla');
            $columnaStock = in_array('stock', $columnasPivote) ? 'stock' : (in_array('cantidad', $columnasPivote) ? 'cantidad' : null);

            // Si vienen cantidades por talla
            if ($request->has('stock_tallas') && is_array($request->stock_tallas)) {
                foreach ($request->stock_tallas as $tallaId => $cantidad) {
                    $cant = (int) $cantidad;
                    if ($cant > 0) {
                        $pivote = ['producto_id' => $productoId, 'talla_id' => $tallaId];
                        if ($columnaStock) {
                            $pivote[$columnaStock] = $cant;
                        }
                        DB::table('producto_talla')->insert($pivote);
                    }
                }
            } 
            // Si viene selección por checkboxes de tallas
            elseif ($request->has('tallas') && is_array($request->tallas)) {
                foreach ($request->tallas as $tallaId) {
                    $pivote = ['producto_id' => $productoId, 'talla_id' => $tallaId];
                    if ($columnaStock) {
                        $pivote[$columnaStock] = 10; // Stock por defecto
                    }
                    DB::table('producto_talla')->insert($pivote);
                }
            }
        }

        return redirect()->route('admin.zapatos.index')->with('success', 'Zapato registrado correctamente.');
    }

    /**
     * Vista para editar zapato
     */
    public function edit($id)
    {
        $zapato     = Producto::with('tallas')->findOrFail($id);
        $producto   = $zapato;
        $categorias = Schema::hasTable('categorias') ? DB::table('categorias')->get() : collect([]);
        $tallas     = Schema::hasTable('tallas') ? DB::table('tallas')->get() : collect([]);

        return view('admin.zapatos.edit', compact('zapato', 'producto', 'categorias', 'tallas'));
    }

    /**
     * Actualizar datos del zapato y sincronizar sus tallas
     */
    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $data = [
            'nombre'      => $request->input('nombre', $producto->nombre),
            'precio'      => $request->input('precio', $producto->precio),
            'descripcion' => $request->input('descripcion', $producto->descripcion),
            'updated_at'  => now()
        ];

        if ($request->has('categoria_id')) {
            $data['categoria_id'] = $request->categoria_id;
        }

        if (Schema::hasColumn('productos', 'genero') && $request->has('genero')) {
            $data['genero'] = $request->genero;
        }

        $fileKey = $request->hasFile('imagen_principal') ? 'imagen_principal' : ($request->hasFile('imagen') ? 'imagen' : null);

        if ($fileKey) {
            try {
                $file         = $request->file($fileKey);
                $cloudName    = env('CLOUDINARY_CLOUD_NAME', 'x5lp98vz');
                $uploadPreset = env('CLOUDINARY_UPLOAD_PRESET', 'sneakerslh_preset');

                $response = Http::timeout(30)->withoutVerifying()
                    ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                    ->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", [
                        'upload_preset' => $uploadPreset,
                    ]);

                if ($response->successful()) {
                    $data['imagen_url'] = $response->json()['secure_url'];
                }
            } catch (\Exception $e) {}
        }

        $totalStock = 0;

        // Actualizar tabla pivote de tallas
        if (Schema::hasTable('producto_talla')) {
            $columnasPivote = Schema::getColumnListing('producto_talla');
            $columnaStock = in_array('stock', $columnasPivote) ? 'stock' : (in_array('cantidad', $columnasPivote) ? 'cantidad' : null);

            // Eliminar tallas previas e insertar el nuevo inventario
            if ($request->has('stock_tallas') || $request->has('tallas')) {
                DB::table('producto_talla')->where('producto_id', $id)->delete();

                if ($request->has('stock_tallas') && is_array($request->stock_tallas)) {
                    foreach ($request->stock_tallas as $tallaId => $cantidad) {
                        $cant = (int) $cantidad;
                        if ($cant > 0) {
                            $totalStock += $cant;
                            $pivote = ['producto_id' => $id, 'talla_id' => $tallaId];
                            if ($columnaStock) {
                                $pivote[$columnaStock] = $cant;
                            }
                            DB::table('producto_talla')->insert($pivote);
                        }
                    }
                } elseif ($request->has('tallas') && is_array($request->tallas)) {
                    foreach ($request->tallas as $tallaId) {
                        $totalStock += 10;
                        $pivote = ['producto_id' => $id, 'talla_id' => $tallaId];
                        if ($columnaStock) {
                            $pivote[$columnaStock] = 10; // Stock por defecto
                        }
                        DB::table('producto_talla')->insert($pivote);
                    }
                }
            } else {
                // Si no se enviaron tallas en el request, consultar el stock actual en DB
                if ($columnaStock) {
                    $totalStock = (int) DB::table('producto_talla')->where('producto_id', $id)->sum($columnaStock);
                }
            }
        }

        // Evaluar Visibilidad / Estado (activo):
        // Si hay stock disponible (> 0), reactivamos automáticamente el producto a activo = 1
        if ($totalStock > 0) {
            $data['activo'] = 1;
        } elseif ($request->has('activo')) {
            $data['activo'] = (int) $request->activo;
        } elseif ($request->has('visibilidad')) {
            $data['activo'] = in_array(strtolower($request->visibilidad), ['1', 'disponible', 'activo', 'habilitado']) ? 1 : 0;
        } else {
            $data['activo'] = 0;
        }

        DB::table('productos')->where('id', $id)->update($data);

        return redirect()->route('admin.zapatos.index')->with('success', 'Zapato actualizado correctamente.');
    }

    /**
     * Eliminar zapato
     */
    public function destroy($id)
    {
        if (Schema::hasTable('producto_talla')) {
            DB::table('producto_talla')->where('producto_id', $id)->delete();
        }
        DB::table('productos')->where('id', $id)->delete();

        return redirect()->route('admin.zapatos.index')->with('success', 'Zapato eliminado.');
    }

    /**
     * Activar o desactivar estado del zapato
     */
    public function toggleEstado($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->activo = !$producto->activo;
        $producto->save();

        return back()->with('success', 'Estado actualizado correctamente.');
    }
}