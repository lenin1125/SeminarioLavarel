<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ZapatoController extends Controller
{
    /**
     * Auxiliar para detectar la columna de stock en la tabla pivote ('stock' o 'cantidad')
     */
    private function getColumnaStock()
    {
        $columnasPivote = DB::getSchemaBuilder()->getColumnListing('producto_talla');
        return in_array('stock', $columnasPivote) ? 'stock' : (in_array('cantidad', $columnasPivote) ? 'cantidad' : null);
    }

    /**
     * Listar todos los zapatos
     */
    public function index(Request $request)
    {
        $columnaStock = $this->getColumnaStock();

        $query = Producto::with(['categoria', 'tallas' => function($q) use ($columnaStock) {
            if ($columnaStock) {
                $q->withPivot($columnaStock);
            }
        }])->orderBy('created_at', 'desc');

        // Por defecto muestra solo zapatos activos. Si envía ?todos=1 incluye deshabilitados.
        if (!$request->boolean('todos')) {
            $query->where('activo', true);
        }

        $zapatos = $query->get();

        return response()->json([
            'success' => true,
            'data'    => $zapatos
        ], 200);
    }

    /**
     * Registrar un nuevo zapato vía API
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'categoria_id' => 'required|exists:categorias,id',
            'nombre'       => 'required|string|max:150|unique:productos,nombre',
            'descripcion'  => 'nullable|string',
            'precio'       => 'required|numeric|min:0',
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

        $zapato = new Producto();
        $zapato->categoria_id = $request->categoria_id;
        $zapato->nombre       = $request->nombre;
        $zapato->descripcion  = $request->descripcion;
        $zapato->precio       = $request->precio;
        $zapato->genero       = $request->genero ?? 'UNISEX';
        $zapato->imagen_url   = $request->imagen_url;
        $zapato->activo       = $request->has('activo') ? filter_var($request->activo, FILTER_VALIDATE_BOOLEAN) : true;
        $zapato->save();

        // Procesar stock por tallas si se envían
        if ($request->has('stock_tallas') && is_array($request->stock_tallas)) {
            $columnaStock = $this->getColumnaStock();
            foreach ($request->stock_tallas as $tallaId => $cantidad) {
                $cant = (int) $cantidad;
                if ($cant > 0) {
                    $dataPivote = ['producto_id' => $zapato->id, 'talla_id' => $tallaId];
                    if ($columnaStock) {
                        $dataPivote[$columnaStock] = $cant;
                    }
                    DB::table('producto_talla')->insert($dataPivote);
                }
            }
        }

        $columnaStock = $this->getColumnaStock();
        $zapato->load(['categoria', 'tallas' => function($q) use ($columnaStock) {
            if ($columnaStock) {
                $q->withPivot($columnaStock);
            }
        }]);

        return response()->json([
            'mensaje' => '¡Zapato registrado con éxito vía API!',
            'datos'   => $zapato
        ], 201);
    }

    /**
     * Mostrar un zapato específico
     */
    public function show($id)
    {
        $columnaStock = $this->getColumnaStock();

        $zapato = Producto::with(['categoria', 'tallas' => function($q) use ($columnaStock) {
            if ($columnaStock) {
                $q->withPivot($columnaStock);
            }
        }])->find($id);

        if (!$zapato) {
            return response()->json([
                'success' => false,
                'mensaje' => 'El zapato solicitado no existe.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'datos'   => $zapato
        ], 200);
    }

    /**
     * Actualizar un zapato existente vía API
     */
    public function update(Request $request, $id)
    {
        $zapato = Producto::find($id);

        if (!$zapato) {
            return response()->json([
                'success' => false,
                'mensaje' => 'El zapato solicitado no existe.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'categoria_id' => 'sometimes|required|exists:categorias,id',
            'nombre'       => 'sometimes|required|string|max:150|unique:productos,nombre,' . $zapato->id,
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

        if ($request->has('categoria_id')) $zapato->categoria_id = $request->categoria_id;
        if ($request->has('nombre'))       $zapato->nombre       = $request->nombre;
        if ($request->has('descripcion'))  $zapato->descripcion  = $request->descripcion;
        if ($request->has('precio'))       $zapato->precio       = $request->precio;
        if ($request->has('genero'))       $zapato->genero       = $request->genero;
        if ($request->has('imagen_url'))   $zapato->imagen_url   = $request->imagen_url;
        if ($request->has('activo'))       $zapato->activo       = filter_var($request->activo, FILTER_VALIDATE_BOOLEAN);

        $zapato->save();

        if ($request->has('stock_tallas') && is_array($request->stock_tallas)) {
            $columnaStock = $this->getColumnaStock();
            DB::table('producto_talla')->where('producto_id', $zapato->id)->delete();

            foreach ($request->stock_tallas as $tallaId => $cantidad) {
                $cant = (int) $cantidad;
                if ($cant > 0) {
                    $dataPivote = ['producto_id' => $zapato->id, 'talla_id' => $tallaId];
                    if ($columnaStock) {
                        $dataPivote[$columnaStock] = $cant;
                    }
                    DB::table('producto_talla')->insert($dataPivote);
                }
            }
        }

        $columnaStock = $this->getColumnaStock();
        $zapato->load(['categoria', 'tallas' => function($q) use ($columnaStock) {
            if ($columnaStock) {
                $q->withPivot($columnaStock);
            }
        }]);

        return response()->json([
            'mensaje' => '¡Zapato actualizado con éxito vía API!',
            'datos'   => $zapato
        ], 200);
    }

    /**
     * Deshabilitar zapato (marcar como inactivo / agotado)
     */
    public function destroy($id)
    {
        $zapato = Producto::find($id);

        if (!$zapato) {
            return response()->json([
                'success' => false,
                'mensaje' => 'El zapato solicitado no existe.'
            ], 404);
        }

        $zapato->activo = false;
        $zapato->save();

        return response()->json([
            'mensaje' => 'El zapato ha sido deshabilitado y marcado como agotado correctamente.'
        ], 200);
    }
}