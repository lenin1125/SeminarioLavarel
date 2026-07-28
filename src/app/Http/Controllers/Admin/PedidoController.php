<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PedidoService;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    protected PedidoService $pedidoService;

    /**
     * Inyección de dependencia de PedidoService
     */
    public function __construct(PedidoService $pedidoService)
    {
        $this->pedidoService = $pedidoService;
    }

    /**
     * Vista de Validar Comprobantes (/admin/pagos)
     */
    public function pagosIndex(Request $request)
    {
        $pagos = $this->pedidoService->obtenerPagosPendientes();
        
        $pagosPorVerificar = $pagos;
        $pedidos = $pagos;

        return view('admin.pagos.index', compact('pagos', 'pedidos', 'pagosPorVerificar'));
    }

    /**
     * Vista de Historial / Consulta de Pedidos (/admin/pedidos)
     */
    public function index(Request $request)
    {
        $filtro = $request->get('estado', 'todos');
        $datos  = $this->pedidoService->obtenerPedidosConFiltro($filtro);

        $pedidos = $datos['pedidos'];
        $conteos = $datos['conteos'];

        return view('admin.pedidos.index', compact('pedidos', 'filtro', 'conteos'));
    }

    /**
     * Acción: Aprobar Pago / Pedido
     */
    public function aprobar($pedido_id)
    {
        $resultado = $this->pedidoService->aprobarPedido((int) $pedido_id);

        return redirect()->back()->with($resultado['status'], $resultado['message']);
    }

    /**
     * Acción: Rechazar Pago / Pedido
     */
    public function rechazar($id)
    {
        $resultado = $this->pedidoService->rechazarPedido((int) $id);

        return redirect()->back()->with($resultado['status'], $resultado['message']);
    }
}