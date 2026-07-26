@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-950 text-gray-100 py-10 -mt-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- RESUMEN DE COMPRA (Columna Izquierda) -->
            <div class="lg:col-span-5 bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-2xl">
                <h2 class="text-xl font-black text-white tracking-tight uppercase border-b border-gray-800 pb-4 mb-6">
                    Resumen de Compra
                </h2>

                <div class="space-y-4 mb-6 divide-y divide-gray-800/60">
                    @php $totalAcumulado = 0; @endphp
                    @foreach($carrito as $item)
                        @php $subtotal = $item['precio'] * $item['cantidad']; $totalAcumulado += $subtotal; @endphp
                        <div class="pt-3 first:pt-0 flex justify-between items-center">
                            <div>
                                <h4 class="text-sm font-bold text-white">{{ $item['nombre'] ?? 'Producto' }}</h4>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    Talla: <span class="text-gray-200 font-semibold">{{ $item['talla'] }}</span> | 
                                    Cantidad: <span class="text-gray-200 font-semibold">{{ $item['cantidad'] }}</span>
                                </p>
                            </div>
                            <span class="text-sm font-black text-white">${{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-gray-800 pt-4 flex justify-between items-center">
                    <span class="text-base font-bold text-gray-300">Total a facturar:</span>
                    <span class="text-xl font-black text-indigo-400">${{ number_format($totalAcumulado, 0, ',', '.') }} COP</span>
                </div>
            </div>

            <!-- FORMULARIO DE ENVÍO (Columna Derecha) -->
            <div class="lg:col-span-7 bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-2xl">
                <h2 class="text-xl font-black text-white tracking-tight uppercase border-b border-gray-800 pb-4 mb-6">
                    Datos de Envío y Facturación
                </h2>

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                            <p>⚠️ {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('checkout.procesar') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Correo Electrónico & Cédula -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1.5">
                                Correo Electrónico <span class="text-gray-500">(Registrado)</span>
                            </label>
                            <input type="email" 
                                   value="{{ Auth::user()->email ?? '' }}" 
                                   readonly 
                                   class="w-full bg-gray-950/80 border border-gray-800 text-gray-400 rounded-xl px-4 py-2.5 text-sm cursor-not-allowed outline-none">
                        </div>

                        <div>
                            <label for="cedula" class="block text-[11px] font-bold uppercase tracking-wider text-gray-300 mb-1.5">
                                Cédula / Doc. Identidad <span class="text-indigo-400">*</span>
                            </label>
                            <input type="text" 
                                   name="cedula" 
                                   id="cedula" 
                                   value="{{ old('cedula') }}" 
                                   placeholder="Ej: 1088123456" 
                                   required 
                                   class="w-full bg-gray-950 border border-gray-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-white placeholder-gray-600 rounded-xl px-4 py-2.5 text-sm outline-none transition-all">
                        </div>
                    </div>

                    <!-- Departamento & Ciudad -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="departamento" class="block text-[11px] font-bold uppercase tracking-wider text-gray-300 mb-1.5">
                                Departamento <span class="text-indigo-400">*</span>
                            </label>
                            <select name="departamento" 
                                    id="departamento" 
                                    required 
                                    onchange="cargarMunicipios()" 
                                    class="w-full bg-gray-950 border border-gray-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-white rounded-xl px-4 py-2.5 text-sm outline-none transition-all cursor-pointer">
                                <option value="" class="bg-gray-900 text-gray-400">Selecciona departamento...</option>
                            </select>
                        </div>

                        <div>
                            <label for="ciudad" class="block text-[11px] font-bold uppercase tracking-wider text-gray-300 mb-1.5">
                                Ciudad / Municipio <span class="text-indigo-400">*</span>
                            </label>
                            <select name="ciudad" 
                                    id="ciudad" 
                                    required 
                                    disabled 
                                    class="w-full bg-gray-950 border border-gray-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-white rounded-xl px-4 py-2.5 text-sm outline-none transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                <option value="" class="bg-gray-900 text-gray-400">Primero selecciona departamento...</option>
                            </select>
                        </div>
                    </div>

                    <!-- Barrio & Teléfono -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="barrio" class="block text-[11px] font-bold uppercase tracking-wider text-gray-300 mb-1.5">
                                Barrio <span class="text-indigo-400">*</span>
                            </label>
                            <input type="text" 
                                   name="barrio" 
                                   id="barrio" 
                                   value="{{ old('barrio') }}" 
                                   placeholder="Ej: El Centro, Las Cruces..." 
                                   required 
                                   class="w-full bg-gray-950 border border-gray-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-white placeholder-gray-600 rounded-xl px-4 py-2.5 text-sm outline-none transition-all">
                        </div>

                        <div>
                            <label for="telefono" class="block text-[11px] font-bold uppercase tracking-wider text-gray-300 mb-1.5">
                                Teléfono de Contacto <span class="text-indigo-400">*</span>
                            </label>
                            <input type="text" 
                                   name="telefono" 
                                   id="telefono" 
                                   value="{{ old('telefono', Auth::user()->telefono ?? '') }}" 
                                   placeholder="Ej: 3101234567" 
                                   required 
                                   class="w-full bg-gray-950 border border-gray-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-white placeholder-gray-600 rounded-xl px-4 py-2.5 text-sm outline-none transition-all">
                        </div>
                    </div>

                    <!-- Dirección de Entrega -->
                    <div>
                        <label for="direccion" class="block text-[11px] font-bold uppercase tracking-wider text-gray-300 mb-1.5">
                            Dirección de Entrega <span class="text-indigo-400">*</span>
                        </label>
                        <input type="text" 
                               name="direccion" 
                               id="direccion" 
                               value="{{ old('direccion') }}" 
                               placeholder="Ej: Carrera 5 # 10-20" 
                               required 
                               class="w-full bg-gray-950 border border-gray-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-white placeholder-gray-600 rounded-xl px-4 py-2.5 text-sm outline-none transition-all">
                    </div>

                    <!-- Indicaciones de Entrega / Anexos -->
                    <div>
                        <label for="indicaciones" class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1.5">
                            Indicaciones de Entrega / Anexos <span class="text-gray-500">(Opcional)</span>
                        </label>
                        <input type="text" 
                               name="indicaciones" 
                               id="indicaciones" 
                               value="{{ old('indicaciones') }}" 
                               placeholder="Ej: Apto 302, Conjunto Res. Los Pinos, dejar en portería" 
                               class="w-full bg-gray-950 border border-gray-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-white placeholder-gray-600 rounded-xl px-4 py-2.5 text-sm outline-none transition-all">
                    </div>

                    <!-- Botón Continuar -->
                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3.5 px-6 rounded-xl shadow-lg shadow-indigo-600/30 transition-all duration-200 cursor-pointer flex items-center justify-center gap-2 text-sm uppercase tracking-wider">
                            <span>Continuar al Pago</span>
                            <span>💳</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- SCRIPT COLOMBIA DEPARTAMENTOS Y MUNICIPIOS -->
<script>
const colombiaData = {
    "Valle del Cauca": ["Cali", "Cartago", "Palmira", "Buenaventura", "Tuluá", "Buga", "Jamundí", "Yumbo", "Sevilla", "Zarzal"],
    "Antioquia": ["Medellín", "Bello", "Itagüí", "Envigado", "Rionegro", "Apartadó", "Turbo", "Caucasia", "Sabaneta", "Chigorodó"],
    "Bogotá D.C.": ["Bogotá D.C."],
    "Cundinamarca": ["Soacha", "Girardot", "Zipaquirá", "Chía", "Facatativá", "Mosquera", "Madrid", "Funza", "Cajicá"],
    "Atlántico": ["Barranquilla", "Soledad", "Malambo", "Sabanalarga", "Baranoa", "Puerto Colombia"],
    "Santander": ["Bucaramanga", "Floridablanca", "Girón", "Piedecuesta", "Barrancabermeja", "San Gil"],
    "Bolívar": ["Cartagena", "Magangué", "Turbaco", "Arjona", "Carmen de Bolívar"],
    "Risaralda": ["Pereira", "Dosquebradas", "Santa Rosa de Cabal", "La Virginia"],
    "Caldas": ["Manizales", "Villamaría", "La Dorada", "Riosucio", "Chinchiná"],
    "Quindío": ["Armenia", "Calarcá", "La Tebaida", "Montenegro", "Quimbaya"],
    "Nariño": ["Pasto", "Tumaco", "Ipiales", "Sandoná"],
    "Tolima": ["Ibagué", "Espinal", "Melgar", "Honda", "Mariquita"],
    "Huila": ["Neiva", "Pitalito", "Garzón", "La Plata"],
    "Córdoba": ["Montería", "Cereté", "Sahagún", "Lorica", "Montelíbano"],
    "Norte de Santander": ["Cúcuta", "Ocaña", "Villa del Rosario", "Los Patios", "Pamplona"],
    "Cesar": ["Valledupar", "Aguachica", "Agustín Codazzi", "Bosconia"],
    "Meta": ["Villavicencio", "Acacías", "Granada", "Puerto López"],
    "Cauca": ["Popayán", "Santander de Quilichao", "Puerto Tejada"],
    "Magdalena": ["Santa Marta", "Ciénaga", "Fundación", "El Banco"],
    "Boyacá": ["Tunja", "Sogamoso", "Duitama", "Chiquinquirá"],
    "La Guajira": ["Riohacha", "Maicao", "Uribia", "Fonseca"],
    "Sucre": ["Sincelejo", "Corozal", "San Marcos"],
    "Chocó": ["Quibdó", "Istmina"],
    "Arauca": ["Arauca", "Tame", "Saravena"],
    "Casanare": ["Yopal", "Aguazul", "Paz de Ariporo"],
    "Caquetá": ["Florencia", "San Vicente del Caguán"],
    "Putumayo": ["Mocoa", "Puerto Asís"],
    "Amazonas": ["Leticia"],
    "San Andrés y Providencia": ["San Andrés"],
    "Guaviare": ["San José del Guaviare"],
    "Vichada": ["Puerto Carreño"],
    "Vaupés": ["Mitú"],
    "Guainía": ["Inírida"]
};

document.addEventListener("DOMContentLoaded", function() {
    const depSelect = document.getElementById('departamento');
    for (let dep in colombiaData) {
        let option = document.createElement('option');
        option.value = dep;
        option.textContent = dep;
        option.className = "bg-gray-900 text-white";
        depSelect.appendChild(option);
    }
});

function cargarMunicipios() {
    const depSelect = document.getElementById('departamento');
    const munSelect = document.getElementById('ciudad');
    const selectedDep = depSelect.value;

    munSelect.innerHTML = '<option value="" class="bg-gray-900 text-gray-400">Selecciona una ciudad...</option>';

    if (selectedDep && colombiaData[selectedDep]) {
        colombiaData[selectedDep].forEach(mun => {
            let option = document.createElement('option');
            option.value = mun;
            option.textContent = mun;
            option.className = "bg-gray-900 text-white";
            munSelect.appendChild(option);
        });
        munSelect.disabled = false;
    } else {
        munSelect.disabled = true;
    }
}
</script>
@endsection