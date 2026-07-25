<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductoController extends Controller
{
    /**
     * Auxiliar para detectar dinámicamente el nombre de la columna stock ('stock' o 'cantidad')
     */
    private function getColumnaStock()
    {
        $columnasPivote = DB::getSchemaBuilder()->getColumnListing('producto_talla');
        return in_array('stock', $columnasPivote) ? 'stock' : (in_array('cantidad', $columnasPivote) ? 'cantidad' : null);
    }

    /**
     * Mostrar el catálogo completo de productos (zapatos)
     */
    public function index(Request $request)
    {
        $columnaStock = $this->getColumnaStock();

        $query = Producto::with(['categoria', 'tallas' => function($q) use ($columnaStock) {
            if ($columnaStock) {
                $q->withPivot($columnaStock);
            }
        }]);

        // Por defecto muestra solo productos activos. Si envía ?todos=1 o ?todos=true muestra todo.
        if (!$request->boolean('todos')) {
            $query->where('activo', true);
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        $productos = $query->get();

        return response()->json([
            'success' => true,
            'data'    => $productos
        ], 200);
    }

    /**
     * Registrar un nuevo zapato (Solo Administrador)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'categoria_id' => 'required|exists:categorias,id',
            'nombre'       => 'required|string|max:150',
            'descripcion'  => 'nullable|string',
            'precio'       => 'required|numeric|min:0',
            'genero'       => 'nullable|in:UNISEX,HOMBRE,MUJER',
            'imagen_url'   => 'nullable|string',
            'activo'       => 'nullable|boolean',
            'stock_tallas' => 'nullable|array', // Formato esperado: {"1": 5, "2": 0} (talla_id => cantidad)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $producto = new Producto();
        $producto->categoria_id = $request->categoria_id;
        $producto->nombre       = $request->nombre;
        $producto->descripcion  = $request->descripcion;
        $producto->precio       = $request->precio;
        $producto->genero       = $request->genero ?? 'UNISEX';
        $producto->imagen_url   = $request->imagen_url;
        $producto->activo       = $request->has('activo') ? filter_var($request->activo, FILTER_VALIDATE_BOOLEAN) : true;
        $producto->save();

        // Procesar inventario por tallas si se envían en el JSON
        if ($request->has('stock_tallas') && is_array($request->stock_tallas)) {
            $columnaStock = $this->getColumnaStock();
            foreach ($request->stock_tallas as $tallaId => $cantidad) {
                $cant = (int) $cantidad;
                if ($cant > 0) {
                    $dataPivote = ['producto_id' => $producto->id, 'talla_id' => $tallaId];
                    if ($columnaStock) {
                        $dataPivote[$columnaStock] = $cant;
                    }
                    DB::table('producto_talla')->insert($dataPivote);
                }
            }
        }

        $columnaStock = $this->getColumnaStock();
        $producto->load(['categoria', 'tallas' => function($q) use ($columnaStock) {
            if ($columnaStock) {
                $q->withPivot($columnaStock);
            }
        }]);

        return response()->json([
            'success' => true,
            'message' => 'Producto registrado exitosamente.',
            'data'    => $producto
        ], 201);
    }

    /**
     * Mostrar un zapato específico
     */
    public function show($id)
    {
        $columnaStock = $this->getColumnaStock();

        $producto = Producto::with(['categoria', 'tallas' => function($q) use ($columnaStock) {
            if ($columnaStock) {
                $q->withPivot($columnaStock);
            }
        }])->find($id);

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $producto
        ], 200);
    }

    /**
     * Actualizar un zapato existente (Solo Administrador)
     */
    public function update(Request $request, $id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'categoria_id' => 'sometimes|required|exists:categorias,id',
            'nombre'       => 'sometimes|required|string|max:150',
            'descripcion'  => 'nullable|string',
            'precio'       => 'sometimes|required|numeric|min:0',
            'genero'       => 'nullable|in:UNISEX,HOMBRE,MUJER',
            'imagen_url'   => 'nullable|string',
            'activo'       => 'nullable|boolean',
            'stock_tallas' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        if ($request->has('categoria_id')) $producto->categoria_id = $request->categoria_id;
        if ($request->has('nombre'))       $producto->nombre       = $request->nombre;
        if ($request->has('descripcion'))  $producto->descripcion  = $request->descripcion;
        if ($request->has('precio'))       $producto->precio       = $request->precio;
        if ($request->has('genero'))       $producto->genero       = $request->genero;
        if ($request->has('imagen_url'))   $producto->imagen_url   = $request->imagen_url;
        if ($request->has('activo'))       $producto->activo       = filter_var($request->activo, FILTER_VALIDATE_BOOLEAN);

        $producto->save();

        // Actualizar tallas si se envían en el JSON
        if ($request->has('stock_tallas') && is_array($request->stock_tallas)) {
            $columnaStock = $this->getColumnaStock();
            DB::table('producto_talla')->where('producto_id', $producto->id)->delete();

            foreach ($request->stock_tallas as $tallaId => $cantidad) {
                $cant = (int) $cantidad;
                if ($cant > 0) {
                    $dataPivote = ['producto_id' => $producto->id, 'talla_id' => $tallaId];
                    if ($columnaStock) {
                        $dataPivote[$columnaStock] = $cant;
                    }
                    DB::table('producto_talla')->insert($dataPivote);
                }
            }
        }

        $columnaStock = $this->getColumnaStock();
        $producto->load(['categoria', 'tallas' => function($q) use ($columnaStock) {
            if ($columnaStock) {
                $q->withPivot($columnaStock);
            }
        }]);

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado exitosamente.',
            'data'    => $producto
        ], 200);
    }

    /**
     * Deshabilitar producto (marcar como inactivo)
     */
    public function destroy($id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado.'
            ], 404);
        }

        $producto->activo = false;
        $producto->save();

        return response()->json([
            'success' => true,
            'message' => 'Producto deshabilitado exitosamente y marcado como agotado.'
        ], 200);
    }
}