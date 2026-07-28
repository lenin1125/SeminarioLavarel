<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class CarritoController extends Controller
{
    /**
     * Agregar un producto al carrito de compras
     */
    public function agregar(Request $request, $id)
    {
        $producto = Producto::with(['tallas' => function($q) {
            $q->withPivot('stock');
        }])->findOrFail($id);

        // 1. VALIDACIÓN: Verificar si el producto está activo
        if (isset($producto->activo) && !$producto->activo) {
            return redirect()->back()->with('error', 'Lo sentimos, este producto se encuentra agotado o fuera de servicio.');
        }

        $carrito = session()->get('carrito', []);
        
        $tallaElegida = $request->input('talla', '39');
        $cantidadDeseada = (int) $request->input('cantidad', 1);
        $itemKey = $id . '_' . $tallaElegida;

        // 2. Buscar el registro de la talla seleccionada
        $tallaModel = $producto->tallas->first(function ($item) use ($tallaElegida) {
            $valorTalla = $item->talla ?? $item->numero ?? $item->nombre ?? $item->id;
            return (string)$valorTalla === (string)$tallaElegida;
        });

        if (!$tallaModel) {
            return redirect()->back()->with('error', 'La talla seleccionada no pertenece a este producto.');
        }

        // 3. Obtener el stock disponible desde la tabla pivote
        $stockDisponible = $tallaModel->pivot->stock ?? 0;

        if ($stockDisponible <= 0) {
            return redirect()->back()->with('error', "La talla EU {$tallaElegida} no cuenta con stock disponible actualmente.");
        }

        $cantidadActualEnCarrito = isset($carrito[$itemKey]) ? $carrito[$itemKey]['cantidad'] : 0;

        // 4. Validar que no supere el stock real
        if (($cantidadActualEnCarrito + $cantidadDeseada) > $stockDisponible) {
            return redirect()->back()->with('error', "No hay suficiente stock. Límite disponible para esta talla: {$stockDisponible} unidades.");
        }

        if (isset($carrito[$itemKey])) {
            $carrito[$itemKey]['cantidad'] += $cantidadDeseada;
            $carrito[$itemKey]['max_stock'] = $stockDisponible;
        } else {
            $carrito[$itemKey] = [
                "id"         => $producto->id,
                "nombre"     => $producto->nombre,
                "cantidad"   => $cantidadDeseada,
                "precio"     => $producto->precio,
                "talla"      => $tallaElegida,
                "imagen_url" => $producto->imagen_url,
                "max_stock"  => $stockDisponible
            ];
        }

        session()->put('carrito', $carrito);
        return redirect()->back()->with('success', 'Producto agregado al carrito exitosamente.');
    }

    /**
     * Ver la vista del carrito
     */
    public function index()
    {
        $carrito = session()->get('carrito', []);

        foreach ($carrito as $key => &$item) {
            $producto = Producto::with(['tallas' => function($q) {
                $q->withPivot('stock');
            }])->find($item['id']);

            $maxStock = 10;

            if ($producto) {
                if (isset($producto->activo) && !$producto->activo) {
                    $maxStock = 0;
                } else {
                    $tallaModel = $producto->tallas->first(function($t) use ($item) {
                        $val = $t->talla ?? $t->numero ?? $t->nombre ?? $t->id;
                        return (string)$val === (string)$item['talla'];
                    });

                    if ($tallaModel && $tallaModel->pivot) {
                        $maxStock = $tallaModel->pivot->stock ?? 10;
                    }
                }
            } else {
                $maxStock = 0;
            }

            $item['max_stock'] = $maxStock;

            if ($item['cantidad'] > $maxStock && $maxStock > 0) {
                $item['cantidad'] = $maxStock;
            }
        }

        unset($item);
        session()->put('carrito', $carrito);

        return view('carrito', compact('carrito'));
    }

    /**
     * Incrementar, decrementar o eliminar ítems del carrito
     */
    public function actualizar(Request $request, $id)
    {
        $carrito = session()->get('carrito', []);
        
        if (isset($carrito[$id])) {
            $maxStock = $carrito[$id]['max_stock'] ?? 10;

            if ($request->accion === 'incrementar') {
                if ($maxStock <= 0) {
                    return redirect()->back()->with('error', 'Este producto ya no se encuentra disponible.');
                }

                if ($carrito[$id]['cantidad'] < $maxStock) {
                    $carrito[$id]['cantidad']++;
                } else {
                    return redirect()->back()->with('error', "Stock máximo disponible alcanzado ({$maxStock} uds).");
                }
            }

            if ($request->accion === 'decrementar') {
                $carrito[$id]['cantidad']--;
                if ($carrito[$id]['cantidad'] <= 0) {
                    unset($carrito[$id]);
                }
            }

            if ($request->accion === 'eliminar') {
                unset($carrito[$id]);
            }

            session()->put('carrito', $carrito);
        }

        return redirect()->route('carrito.index');
    }
}