<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Talla;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ZapatoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with('categoria');

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        $zapatos = $query->get();
        $categorias = Categoria::all();

        return view('zapatos.index', compact('zapatos', 'categorias'));
    }

    public function create()
    {
        $categorias = Categoria::all();
        $tallas = Talla::all();
        return view('zapatos.create', compact('categorias', 'tallas'));
    }

    public function store(Request $request)
    {
        $cloudName = 'x5lp98vz';
        $uploadPreset = 'sneakerslh_preset';
        
        $filePrincipal = $request->file('imagen_principal') ?? $request->file('imagen');
        
        $imagenUrl = null;
        if ($filePrincipal) {
            $responsePrincipal = Http::attach('file', file_get_contents($filePrincipal->getRealPath()), $filePrincipal->getClientOriginalName())
                ->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", ['upload_preset' => $uploadPreset]);

            if ($responsePrincipal->failed()) {
                return back()->with('error', 'Error al subir la imagen a Cloudinary.');
            }
            
            $dataPrincipal = $responsePrincipal->json();
            $imagenUrl = $dataPrincipal['secure_url'];
        }

        $producto = new Producto();
        $producto->nombre       = $request->nombre;
        $producto->descripcion  = $request->descripcion;
        $producto->precio       = $request->precio;
        $producto->genero       = $request->genero ?? 'UNISEX';
        $producto->imagen_url   = $imagenUrl;
        $producto->categoria_id = $request->categoria_id;
        $producto->activo       = $request->has('activo') ? filter_var($request->activo, FILTER_VALIDATE_BOOLEAN) : true;
        $producto->save();

        $tallasData = $request->input('stock_tallas') ?? $request->input('tallas');

        if (is_array($tallasData)) {
            $columnasPivote = DB::getSchemaBuilder()->getColumnListing('producto_talla');
            $columnaStock = in_array('stock', $columnasPivote) ? 'stock' : (in_array('cantidad', $columnasPivote) ? 'cantidad' : null);

            foreach ($tallasData as $key => $cantidad) {
                $cant = (int) $cantidad;
                if ($cant > 0) {
                    $tallaId = $this->obtenerOCrearTallaId($key);

                    $dataPivote = ['producto_id' => $producto->id, 'talla_id' => $tallaId];
                    if ($columnaStock) {
                        $dataPivote[$columnaStock] = $cant;
                    }

                    DB::table('producto_talla')->insert($dataPivote);
                }
            }
        }

        return redirect()->route('admin.zapatos.index')->with('success', 'Zapato guardado exitosamente.');
    }

    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        $categorias = Categoria::all();
        $tallas = Talla::all();
        return view('zapatos.edit', compact('producto', 'categorias', 'tallas'));
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        // Asignación directa
        $producto->nombre       = $request->nombre;
        $producto->descripcion  = $request->descripcion;
        $producto->precio       = $request->precio;
        $producto->genero       = $request->genero ?? 'UNISEX';
        $producto->categoria_id = $request->categoria_id;

        // Forzar actualización de 'activo'
        if ($request->has('activo')) {
            $producto->activo = filter_var($request->activo, FILTER_VALIDATE_BOOLEAN);
        }

        // Subida de imagen opcional
        $file = $request->file('imagen_principal') ?? $request->file('imagen');
        if ($file) {
            $cloudName = 'x5lp98vz';
            $uploadPreset = 'sneakerslh_preset';

            $response = Http::attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", ['upload_preset' => $uploadPreset]);

            if ($response->successful()) {
                $producto->imagen_url = $response->json()['secure_url'];
            }
        }

        // Guardar cambios en la base de datos
        $producto->save();

        // Actualizar inventario de tallas
        $tallasData = $request->input('stock_tallas') ?? $request->input('tallas');

        if (is_array($tallasData)) {
            $columnasPivote = DB::getSchemaBuilder()->getColumnListing('producto_talla');
            $columnaStock = in_array('stock', $columnasPivote) ? 'stock' : (in_array('cantidad', $columnasPivote) ? 'cantidad' : null);

            DB::table('producto_talla')->where('producto_id', $producto->id)->delete();

            foreach ($tallasData as $key => $cantidad) {
                $cant = (int) $cantidad;
                if ($cant > 0) {
                    $tallaId = $this->obtenerOCrearTallaId($key);

                    $dataPivote = [
                        'producto_id' => $producto->id,
                        'talla_id'    => $tallaId,
                    ];

                    if ($columnaStock) {
                        $dataPivote[$columnaStock] = $cant;
                    }

                    DB::table('producto_talla')->insert($dataPivote);
                }
            }
        }

        return redirect()->route('admin.zapatos.index')->with('success', "El producto '{$producto->nombre}' fue actualizado con éxito.");
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->activo = false;
        $producto->save();

        return redirect()->back()->with('success', 'El producto ha sido deshabilitado correctamente.');
    }

    public function toggleEstado($id)
    {
        $producto = Producto::findOrFail($id); 
        $producto->activo = !$producto->activo;
        $producto->save();

        $estadoText = $producto->activo ? 'activado y disponible' : 'deshabilitado y marcado como agotado';

        return redirect()->back()->with('success', "El producto '{$producto->nombre}' fue {$estadoText}.");
    }

    private function obtenerOCrearTallaId($key)
    {
        if (is_numeric($key) && Talla::where('id', $key)->exists()) {
            return (int) $key;
        }

        $columnasTallas = DB::getSchemaBuilder()->getColumnListing('tallas');
        
        $columnaValida = null;
        foreach (['numero', 'nombre', 'talla', 'size', 'descripcion'] as $posible) {
            if (in_array($posible, $columnasTallas)) {
                $columnaValida = $posible;
                break;
            }
        }

        if ($columnaValida) {
            $talla = Talla::firstOrCreate([$columnaValida => (string) $key]);
            return $talla->id;
        }

        return (int) $key;
    }
}