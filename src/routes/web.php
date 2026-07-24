<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Talla;
use App\Models\ProductoImagen;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Rutas Públicas: Catálogo e Información General (RF-004)
|--------------------------------------------------------------------------
*/

// Catálogo Principal
Route::get('/', function (Request $request) {
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
})->name('tienda.index');

// Ver Detalle del Zapato
Route::get('/zapatos/{id}', function ($id) {
    $producto = Producto::with(['categoria', 'imagenes', 'tallas'])->findOrFail($id);

    return view('zapatos.show', compact('producto'));
})->name('tienda.show');

// Guardado Genérico por Tallas
Route::post('/zapatos/guardar', function (Request $request) {
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
})->name('zapatos.store');


/*
|--------------------------------------------------------------------------
| Sistema de Autenticación Completo (RF-001)
|--------------------------------------------------------------------------
*/
Route::get('/login', function () {
    if (Auth::check()) return redirect()->route('tienda.index');
    return view('auth.login'); 
})->name('login');

Route::post('/login', function (Request $request) {
    $user = User::where('email', $request->email)->first();
    if ($user && Hash::check($request->password, $user->password)) {
        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->route('tienda.index');
    }
    return back()->with('error', 'Credenciales incorrectas.');
});

Route::get('/registro', function () {
    if (Auth::check()) return redirect()->route('tienda.index');
    return view('auth.register');
})->name('register');

Route::post('/registro', function (Request $request) {
    $partesNombre = explode(' ', trim($request->name), 2);
    $user = User::create([
        'nombre' => $partesNombre[0],
        'apellido' => $partesNombre[1] ?? '.',
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'rol_id' => 2, 
    ]);
    Auth::login($user); 
    return redirect()->route('tienda.index');
})->name('register.store');

Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('tienda.index');
})->name('logout');


/*
|--------------------------------------------------------------------------
| Rutas Protegidas del Carrito y Pedidos (RF-005, RF-006, RF-007)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    // Agregar al Carrito con Validación de Stock Máximo
    Route::post('/carrito/agregar/{id}', function (Request $request, $id) {
        $columnasPivote = DB::getSchemaBuilder()->getColumnListing('producto_talla');
        $columnaStock = in_array('stock', $columnasPivote) ? 'stock' : 'cantidad';

        $producto = Producto::with(['tallas' => function($q) use ($columnaStock) {
            $q->withPivot($columnaStock);
        }])->findOrFail($id);

        $carrito = session()->get('carrito', []);
        
        $tallaElegida = $request->input('talla', '39');
        $cantidadDeseada = (int) $request->input('cantidad', 1);
        $itemKey = $id . '_' . $tallaElegida;

        // Buscar el registro de la talla seleccionada
        $tallaModel = $producto->tallas->first(function ($item) use ($tallaElegida) {
            $valorTalla = $item->talla ?? $item->numero ?? $item->nombre ?? $item->id;
            return (string)$valorTalla === (string)$tallaElegida;
        });

        if (!$tallaModel) {
            return redirect()->back()->with('error', 'La talla seleccionada no pertenece a este producto.');
        }

        // Obtener el stock disponible de la tabla pivote de manera dinámica
        $stockDisponible = $tallaModel->pivot->{$columnaStock} ?? 0;
        $cantidadActualEnCarrito = isset($carrito[$itemKey]) ? $carrito[$itemKey]['cantidad'] : 0;

        if (($cantidadActualEnCarrito + $cantidadDeseada) > $stockDisponible) {
            return redirect()->back()->with('error', "No hay suficiente stock. Límite disponible: {$stockDisponible} unidades.");
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
        return redirect()->back()->with('success', 'Producto agregado al carrito.');
    })->name('carrito.agregar');

    // Ver Carrito (Sincroniza dinámicamente el stock real de cada ítem)
    Route::get('/carrito', function () {
        $carrito = session()->get('carrito', []);

        // Detectamos la columna pivote real para evitar el error 1054
        $columnasPivote = DB::getSchemaBuilder()->getColumnListing('producto_talla');
        $columnaStock = in_array('stock', $columnasPivote) ? 'stock' : 'cantidad';

        foreach ($carrito as $key => &$item) {
            $producto = Producto::with(['tallas' => function($q) use ($columnaStock) {
                $q->withPivot($columnaStock);
            }])->find($item['id']);

            $maxStock = 10;

            if ($producto) {
                $tallaModel = $producto->tallas->first(function($t) use ($item) {
                    $val = $t->talla ?? $t->numero ?? $t->nombre ?? $t->id;
                    return (string)$val === (string)$item['talla'];
                });

                if ($tallaModel && $tallaModel->pivot) {
                    $maxStock = $tallaModel->pivot->{$columnaStock} ?? 10;
                }
            }

            $item['max_stock'] = $maxStock;

            // Ajustar si supera la existencia en base de datos
            if ($item['cantidad'] > $maxStock) {
                $item['cantidad'] = $maxStock;
            }
        }

        session()->put('carrito', $carrito);

        return view('carrito', compact('carrito'));
    })->name('carrito.index');

    // Actualizar Carrito (Bloquea incrementos por encima del max_stock)
    Route::post('/carrito/actualizar/{id}', function (Request $request, $id) {
        $carrito = session()->get('carrito', []);
        
        if (isset($carrito[$id])) {
            $maxStock = $carrito[$id]['max_stock'] ?? 10;

            if ($request->accion === 'incrementar') {
                if ($carrito[$id]['cantidad'] < $maxStock) {
                    $carrito[$id]['cantidad']++;
                } else {
                    return redirect()->back()->with('error', "No puedes agregar más unidades. Stock máximo disponible alcanzado ({$maxStock} uds).");
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
    })->name('carrito.actualizar');

    Route::get('/checkout', function () {
        $carrito = session()->get('carrito', []);
        if (empty($carrito)) return redirect()->route('tienda.index');
        return view('checkout', compact('carrito'));
    })->name('checkout.index');

    Route::post('/checkout/procesar', function (Request $request) {
        $carrito = session()->get('carrito', []);
        $total = 0;
        foreach($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        $user = Auth::user();
        $columnasPedidos = DB::getSchemaBuilder()->getColumnListing('pedidos');

        $datosPedido = [
            'usuario_id' => Auth::id(),
            'total'      => $total,
            'created_at' => now(),
            'updated_at' => now()
        ];

        if (in_array('telefono', $columnasPedidos)) {
            $datosPedido['telefono'] = $request->input('telefono', $user->telefono ?? null);
        }
        if (in_array('direccion', $columnasPedidos)) {
            $datosPedido['direccion'] = $request->input('direccion', 'Dirección no especificada');
        }
        if (in_array('ciudad', $columnasPedidos)) {
            $datosPedido['ciudad'] = $request->input('ciudad', 'N/A');
        }
        if (in_array('departamento', $columnasPedidos)) {
            $datosPedido['departamento'] = $request->input('departamento', 'N/A');
        }

        $pedidoId = DB::table('pedidos')->insertGetId($datosPedido);

        foreach($carrito as $item) {
            DB::table('detalle_pedido')->insert([
                'pedido_id'       => $pedidoId,
                'producto_id'     => $item['id'],
                'cantidad'        => $item['cantidad'],
                'talla'           => $item['talla'],
                'precio_unitario' => $item['precio']
            ]);
        }
        
        session()->put('ultimo_pedido_id', $pedidoId);
        return redirect()->route('checkout.pago_pantalla');
    })->name('checkout.procesar');

    Route::get('/checkout/adjuntar-pago', function() {
        $pedidoId = session()->get('ultimo_pedido_id');
        if(!$pedidoId) return redirect()->route('tienda.index');
        return view('checkout_pago', compact('pedidoId'));
    })->name('checkout.pago_pantalla');

   Route::post('/checkout/guardar-pago', function(Request $request) {
    // 1. Validamos extensión y tamaño máximo (5MB)
    $request->validate([
        'comprobante' => 'required|mimes:jpeg,png,jpg,pdf|max:5120'
    ]);
    
    $cloudName = 'x5lp98vz';
    $uploadPreset = 'sneakerslh_preset';
    $file = $request->file('comprobante');

    // 2. Si es PDF usamos 'raw', si es imagen usamos 'image'
    $extension = strtolower($file->getClientOriginalExtension());
    $resourceType = ($extension === 'pdf') ? 'raw' : 'image';

    $response = Http::attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
        ->post("https://api.cloudinary.com/v1_1/{$cloudName}/{$resourceType}/upload", [
            'upload_preset' => $uploadPreset,
        ]);

    if ($response->failed()) {
        return redirect()->back()->with('error', 'Error al subir el comprobante. Verifica que el archivo no esté corrupto.');
    }

    $data = $response->json();

    $columnas = DB::getSchemaBuilder()->getColumnListing('pagos');
    
    $columnaImagen = 'comprobante'; 
    if (in_array('comprobante_pago', $columnas)) $columnaImagen = 'comprobante_pago';
    elseif (in_array('imagen', $columnas)) $columnaImagen = 'imagen';
    elseif (in_array('url', $columnas)) $columnaImagen = 'url';
    elseif (in_array('comprobante_url', $columnas)) $columnaImagen = 'comprobante_url';

    $insertData = [
        'pedido_id'   => $request->pedido_id,
        'metodo_pago' => 'Transferencia / Nequi / Daviplata',
        $columnaImagen => $data['secure_url'],
        'created_at'  => now(),
        'updated_at'  => now()
    ];

    if (in_array('fecha_pago', $columnas)) {
        $insertData['fecha_pago'] = now();
    }

    DB::table('pagos')->insert($insertData);

    session()->forget('carrito');
    return redirect()->route('tienda.index')->with('success', '¡Comprobante enviado exitosamente!');
})->name('checkout.guardar_pago');

});

/*
|--------------------------------------------------------------------------
| Panel Administrativo (RF-002, RF-003, RF-008, RF-009, RF-010, RF-011)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth'])->group(function () {

    Route::get('/zapatos', function () {
        if (Auth::user()->email !== 'admin@sneakerslh.com') abort(403);
        
        $zapatos = Producto::with('categoria')->get();
        $productos = $zapatos;
        
        return view('zapatos.index', compact('zapatos', 'productos'));
    })->name('admin.zapatos.index');

    Route::get('/zapatos/crear', function () {
        if (Auth::user()->email !== 'admin@sneakerslh.com') abort(403);
        $categorias = Categoria::all();
        $tallas = Talla::all();
        return view('zapatos.create', compact('categorias', 'tallas'));
    })->name('admin.zapatos.create');

    Route::post('/zapatos', function (Request $request) {
        if (Auth::user()->email !== 'admin@sneakerslh.com') abort(403);
        $cloudName = 'x5lp98vz';
        $uploadPreset = 'sneakerslh_preset';
        $filePrincipal = $request->file('imagen_principal');
        
        $responsePrincipal = Http::attach('file', file_get_contents($filePrincipal->getRealPath()), $filePrincipal->getClientOriginalName())
            ->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", ['upload_preset' => $uploadPreset]);

        if ($responsePrincipal->failed()) return 'Error en Cloudinary.';
        $dataPrincipal = $responsePrincipal->json();
        
        $producto = Producto::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'genero' => $request->genero,
            'imagen_url' => $dataPrincipal['secure_url'], 
            'categoria_id' => $request->categoria_id,
        ]);
        
        if ($request->has('stock_tallas') && is_array($request->stock_tallas)) {
            foreach ($request->stock_tallas as $tallaId => $cantidad) {
                $cant = (int) $cantidad;
                if ($cant > 0) {
                    $columnasPivote = DB::getSchemaBuilder()->getColumnListing('producto_talla');
                    $dataPivote = ['producto_id' => $producto->id, 'talla_id' => $tallaId];
                    
                    if (in_array('stock', $columnasPivote)) {
                        $dataPivote['stock'] = $cant;
                    } elseif (in_array('cantidad', $columnasPivote)) {
                        $dataPivote['cantidad'] = $cant;
                    }

                    DB::table('producto_talla')->insert($dataPivote);
                }
            }
        } elseif ($request->has('tallas') && is_array($request->tallas)) {
            $producto->tallas()->attach($request->tallas);
        }

        return redirect()->route('admin.zapatos.index')->with('success', 'Zapato guardado exitosamente.');
    })->name('admin.zapatos.store');

    Route::get('/zapatos/{id}/editar', function ($id) {
        if (Auth::user()->email !== 'admin@sneakerslh.com') abort(403);
        $producto = Producto::findOrFail($id);
        $categorias = Categoria::all();
        $tallas = Talla::all();
        return view('zapatos.edit', compact('producto', 'categorias', 'tallas'));
    })->name('admin.zapatos.edit');

    Route::put('/zapatos/{id}', function (Request $request, $id) {
        if (Auth::user()->email !== 'admin@sneakerslh.com') abort(403);
        $producto = Producto::findOrFail($id);
        
        $data = [
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'genero' => $request->genero,
            'categoria_id' => $request->categoria_id,
        ];

        if ($request->hasFile('imagen_principal')) {
            $cloudName = 'x5lp98vz';
            $uploadPreset = 'sneakerslh_preset';
            $file = $request->file('imagen_principal');
            $response = Http::attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", ['upload_preset' => $uploadPreset]);
            
            if ($response->successful()) {
                $data['imagen_url'] = $response->json()['secure_url'];
            }
        }

        $producto->update($data);

        if ($request->has('stock_tallas') && is_array($request->stock_tallas)) {
            DB::table('producto_talla')->where('producto_id', $producto->id)->delete();
            foreach ($request->stock_tallas as $tallaId => $cantidad) {
                $cant = (int) $cantidad;
                if ($cant > 0) {
                    $columnasPivote = DB::getSchemaBuilder()->getColumnListing('producto_talla');
                    $dataPivote = ['producto_id' => $producto->id, 'talla_id' => $tallaId];
                    if (in_array('stock', $columnasPivote)) {
                        $dataPivote['stock'] = $cant;
                    } elseif (in_array('cantidad', $columnasPivote)) {
                        $dataPivote['cantidad'] = $cant;
                    }
                    DB::table('producto_talla')->insert($dataPivote);
                }
            }
        } elseif ($request->has('tallas')) {
            $producto->tallas()->sync($request->tallas);
        }

        return redirect()->route('admin.zapatos.index')->with('success', 'Producto actualizado.');
    })->name('admin.zapatos.update');

    Route::delete('/zapatos/{id}', function ($id) {
        if (Auth::user()->email !== 'admin@sneakerslh.com') abort(403);
        $producto = Producto::findOrFail($id);
        $producto->tallas()->detach(); 
        $producto->delete();
        return redirect()->route('admin.zapatos.index')->with('success', 'Producto removido del catálogo.');
    })->name('admin.zapatos.destroy');

    Route::get('/proveedores', function() {
        if (Auth::user()->email !== 'admin@sneakerslh.com') abort(403);
        $proveedores = DB::table('proveedores')->get();
        return view('admin.proveedores.index', compact('proveedores'));
    })->name('admin.proveedores.index');

    Route::post('/proveedores', function(Request $request) {
        if (Auth::user()->email !== 'admin@sneakerslh.com') abort(403);
        DB::table('proveedores')->insert([
            'nombre' => $request->nombre,
            'telefono' => $request->telefono,
            'ciudad' => $request->ciudad,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        return redirect()->back()->with('success', 'Proveedor registrado.');
    })->name('admin.proveedores.store');

    Route::get('/pagos', function() {
        if (Auth::user()->email !== 'admin@sneakerslh.com') abort(403);
        
        $columnasPagos = DB::getSchemaBuilder()->getColumnListing('pagos');
        $columnaImagen = 'comprobante'; 
        if (in_array('comprobante_pago', $columnasPagos)) $columnaImagen = 'comprobante_pago';
        elseif (in_array('imagen', $columnasPagos)) $columnaImagen = 'imagen';
        elseif (in_array('url', $columnasPagos)) $columnaImagen = 'url';
        elseif (in_array('comprobante_url', $columnasPagos)) $columnaImagen = 'comprobante_url';

        $pagosPorVerificar = DB::table('pagos')
            ->join('pedidos', 'pagos.pedido_id', '=', 'pedidos.id')
            ->join('users', 'pedidos.usuario_id', '=', 'users.id')
            ->select(
                'pagos.id as pago_id', 
                'pagos.pedido_id', 
                "pagos.{$columnaImagen} as comprobante", 
                'pedidos.total', 
                'users.nombre', 
                'users.apellido'
            )
            ->orderBy('pagos.id', 'asc')
            ->get();
            
        return view('admin.pagos.index', compact('pagosPorVerificar'));
    })->name('admin.pagos.index');

    Route::post('/pedidos/aprobar/{pedido_id}', function($pedido_id) {
        if (Auth::user()->email !== 'admin@sneakerslh.com') abort(403);
        
        $pedido = DB::table('pedidos')->where('id', $pedido_id)->first();
        if (!$pedido) {
            return redirect()->back()->with('error', 'El pedido no existe.');
        }

        DB::table('ventas')->insert([
            'pedido_id'   => $pedido_id,
            'monto_total' => $pedido->total, 
            'created_at'  => now(),
            'updated_at'  => now()
        ]);

        $columnasPedidos = DB::getSchemaBuilder()->getColumnListing('pedidos');
        $updateData = ['updated_at' => now()];
        if (in_array('estado', $columnasPedidos)) {
            $updateData['estado'] = 'pagado';
        }
        DB::table('pedidos')->where('id', $pedido_id)->update($updateData);

        DB::table('pagos')->where('pedido_id', $pedido_id)->delete();

        return redirect()->back()->with('success', '¡Pago verificado y venta asentada con éxito!');
    })->name('admin.pedidos.aprobar');

    Route::get('/reportes', function() {
        if (Auth::user()->email !== 'admin@sneakerslh.com') abort(403);
        
        $ventasDiarias = DB::table('ventas')
            ->whereDate('created_at', now()->toDateString())
            ->sum('monto_total');

        $ventasMensuales = DB::table('ventas')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('monto_total');

        $productoMasVendido = DB::table('detalle_pedido')
            ->join('productos', 'detalle_pedido.producto_id', '=', 'productos.id')
            ->join('ventas', 'detalle_pedido.pedido_id', '=', 'ventas.pedido_id')
            ->select('productos.nombre', DB::raw('SUM(detalle_pedido.cantidad) as total_vendido'))
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('total_vendido')
            ->first();

        return view('admin.reportes', compact('ventasDiarias', 'ventasMensuales', 'productoMasVendido'));
    })->name('admin.reportes.index');

    Route::get('/pedidos-confirmados', function() {
        if (Auth::user()->email !== 'admin@sneakerslh.com') abort(403);

        $pedidosConfirmados = DB::table('ventas')
            ->join('pedidos', 'ventas.pedido_id', '=', 'pedidos.id')
            ->leftJoin('users', 'pedidos.usuario_id', '=', 'users.id')
            ->select(
                'ventas.id as consecutivo_confirmado',
                'pedidos.id as pedido_id',
                'users.nombre as user_nombre',
                'users.apellido as user_apellido',
                'users.email as user_email',
                DB::raw("COALESCE(NULLIF(pedidos.telefono, ''), users.telefono) as telefono_final"),
                'pedidos.direccion',
                'pedidos.ciudad',
                'pedidos.departamento',
                'ventas.monto_total',
                'ventas.created_at as fecha_confirmacion'
            )
            ->orderBy('ventas.id', 'desc')
            ->paginate(10);

        return view('admin.pedidos_confirmados', compact('pedidosConfirmados'));
    })->name('admin.pedidos.confirmados');

    Route::get('/usuarios', function () {
        if (Auth::user()->email !== 'admin@sneakerslh.com') abort(403);

        $usuarios = DB::table('users')
            ->leftJoin('pedidos', 'users.id', '=', 'pedidos.usuario_id')
            ->select(
                'users.id',
                'users.nombre',
                'users.apellido',
                'users.email',
                'users.created_at',
                DB::raw('MAX(pedidos.telefono) as telefono')
            )
            ->groupBy('users.id', 'users.nombre', 'users.apellido', 'users.email', 'users.created_at')
            ->orderBy('users.created_at', 'desc')
            ->paginate(10);

        return view('admin.usuarios', compact('usuarios'));
    })->name('admin.usuarios.index');

    Route::delete('/usuarios/{id}', function ($id) {
        if (Auth::user()->email !== 'admin@sneakerslh.com') abort(403);

        if ((int)$id === Auth::id()) {
            return back()->with('error', 'No puedes eliminar la cuenta de administrador activa.');
        }

        DB::table('users')->where('id', $id)->delete();

        return back()->with('success', 'El usuario ha sido eliminado del sistema.');
    })->name('admin.usuarios.destroy');

    Route::delete('/admin/pedidos/{id}/rechazar', function ($id) {
        if (Auth::user()->email !== 'admin@sneakerslh.com') abort(403);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('pedidos')->where('id', $id)->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return back()->with('success', 'El pedido y su comprobante fueron rechazados y eliminados con éxito.');
    })->name('admin.pedidos.rechazar');

});