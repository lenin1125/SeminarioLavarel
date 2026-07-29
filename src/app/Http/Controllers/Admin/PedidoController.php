<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PedidoService;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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

    /**
     * Generar y descargar reporte en PDF de pedidos por rango de fechas
     */
    public function exportarPdf(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $fechaInicio = $request->fecha_inicio;
        $fechaFin    = $request->fecha_fin;

        // Consultar compras realizadas dentro del rango de fechas
        $pedidos = Pedido::with(['usuario', 'detalles.producto', 'pago'])
            ->whereBetween('created_at', [
                $fechaInicio . ' 00:00:00',
                $fechaFin . ' 23:59:59'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('admin.pedidos.pdf_reporte', compact('pedidos', 'fechaInicio', 'fechaFin'))
            ->setPaper('a4', 'portrait')
            ->setOption([
                'isRemoteEnabled'      => true,
                'isHtml5ParserEnabled' => true
            ]);

        return $pdf->download("reporte_pedidos_{$fechaInicio}_a_{$fechaFin}.pdf");
    }
}