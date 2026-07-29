@foreach($pedidos as $pedido)
    @php
        $tieneUsuarioActivoModal = !empty($pedido->user_nombre);
        $nombreClienteModal = $tieneUsuarioActivoModal ? trim($pedido->user_nombre . ' ' . $pedido->user_apellido) : 'Cliente No Registrado';
    @endphp

    <!-- MODAL 1: DATOS DE ENVÍO -->
    <div id="modal-envio-{{ $pedido->pedido_id }}" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm hidden">
        <div class="bg-gray-900 border border-gray-800 rounded-2xl max-w-lg w-full m-4 overflow-hidden shadow-2xl">
            <div class="p-5 bg-gray-950 border-b border-gray-800 flex justify-between items-center">
                <h3 class="text-base font-black text-white uppercase tracking-wider flex items-center gap-2">
                    📍 Ficha de Envío — Pedido #PED-{{ str_pad($pedido->pedido_id, 4, '0', STR_PAD_LEFT) }}
                </h3>
                <button type="button" onclick="closeModal('modal-envio-{{ $pedido->pedido_id }}')" class="text-gray-400 hover:text-white font-bold text-lg cursor-pointer">&times;</button>
            </div>

            <div class="p-6 space-y-4 text-sm divide-y divide-gray-800/60">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pb-3">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">👤 Destinatario</span>
                        <strong class="text-white font-bold block truncate">{{ $nombreClienteModal }}</strong>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-400 block mb-1">🆔 Cédula / CC</span>
                        <span class="text-white font-bold block">
                            {{ !empty($pedido->cedula) ? $pedido->cedula : (!empty($pedido->user_cedula) ? $pedido->user_cedula : 'No especificada') }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">📞 Teléfono</span>
                        <span class="text-emerald-400 font-bold block">
                            {{ !empty($pedido->telefono_final) ? $pedido->telefono_final : (!empty($pedido->telefono) ? $pedido->telefono : 'No especificado') }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-3 pb-3">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">MAPA Departamento</span>
                        <span class="text-gray-200 font-semibold">{{ !empty($pedido->departamento) ? $pedido->departamento : 'No registrado' }}</span>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">🏙️ Ciudad</span>
                        <span class="text-gray-200 font-semibold">{{ !empty($pedido->ciudad) ? $pedido->ciudad : 'No registrado' }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-3 pb-3">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-400 block mb-1">🏘️ Barrio</span>
                        @if(!empty($pedido->barrio))
                            <span class="text-indigo-200 font-bold bg-indigo-500/10 border border-indigo-500/20 px-3 py-1 rounded-lg inline-block">
                                {{ $pedido->barrio }}
                            </span>
                        @else
                            <span class="text-gray-500 text-xs italic">No especificado</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">📝 Indicaciones</span>
                        <span class="text-gray-300 text-xs block">
                            {{ !empty($pedido->indicaciones) ? $pedido->indicaciones : 'Sin observaciones' }}
                        </span>
                    </div>
                </div>

                <div class="pt-3 pb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">🏠 Dirección Exacta</span>
                    <span class="text-white font-bold block">{{ !empty($pedido->direccion) ? $pedido->direccion : 'No especificada' }}</span>
                </div>
            </div>

            <div class="p-4 bg-gray-950 border-t border-gray-800 flex justify-end">
                <button type="button" onclick="closeModal('modal-envio-{{ $pedido->pedido_id }}')" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 font-bold text-xs rounded-xl cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 2: PRODUCTOS DEL PEDIDO -->
    <div id="modal-productos-conf-{{ $pedido->pedido_id }}" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm hidden">
        <div class="bg-gray-900 border border-gray-800 rounded-2xl max-w-lg w-full m-4 overflow-hidden shadow-2xl">
            <div class="p-5 bg-gray-950 border-b border-gray-800 flex justify-between items-center">
                <h3 class="text-base font-black text-white uppercase tracking-wider flex items-center gap-2">
                    📦 Productos del Pedido #PED-{{ str_pad($pedido->pedido_id, 4, '0', STR_PAD_LEFT) }}
                </h3>
                <button type="button" onclick="closeModal('modal-productos-conf-{{ $pedido->pedido_id }}')" class="text-gray-400 hover:text-white font-bold text-lg cursor-pointer">&times;</button>
            </div>

            <div class="p-6 space-y-3 max-h-80 overflow-y-auto">
                @forelse($pedido->detalles ?? [] as $item)
                    @php
                        $itemObj  = (object)$item;
                        $nombre   = $itemObj->producto_nombre ?? $itemObj->nombre ?? 'Producto #' . ($itemObj->producto_id ?? '');
                        $talla    = $itemObj->talla ?? $itemObj->numero ?? 'N/A';
                        $cantidad = $itemObj->cantidad ?? $itemObj->cant ?? 1;
                        $precio   = $itemObj->precio_unitario ?? $itemObj->precio ?? 0;
                        $imagen   = $itemObj->producto_imagen ?? $itemObj->imagen ?? null;
                    @endphp

                    <div class="flex justify-between items-center bg-gray-950 p-3.5 rounded-xl border border-gray-800">
                        <div class="flex items-center gap-3">
                            @if($imagen)
                                <img src="{{ asset($imagen) }}" alt="{{ $nombre }}" class="w-12 h-12 object-cover rounded-lg border border-gray-800" onerror="this.style.display='none'">
                            @endif
                            <div>
                                <h4 class="text-sm font-bold text-white">{{ $nombre }}</h4>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    Talla: <span class="text-indigo-400 font-bold">{{ $talla }}</span> | 
                                    Cantidad: <span class="text-indigo-400 font-bold">{{ $cantidad }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block text-[11px] text-gray-400">Unitario: ${{ number_format($precio, 0, ',', '.') }}</span>
                            <span class="text-sm font-black text-emerald-400">${{ number_format($precio * $cantidad, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <p class="text-sm italic">Sin desglose detallado registrado para esta venta.</p>
                    </div>
                @endforelse
            </div>

            <div class="p-4 bg-gray-950 border-t border-gray-800 flex justify-between items-center">
                <span class="text-xs font-bold text-gray-400">Monto total pagado:</span>
                <span class="text-base font-black text-emerald-400">${{ number_format($pedido->monto_total, 0, ',', '.') }} COP</span>
            </div>
        </div>
    </div>
@endforeach