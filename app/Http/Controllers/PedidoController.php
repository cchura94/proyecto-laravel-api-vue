<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pedidos = Pedido::with(['cliente', 'productos'])->get();

        return response()->json($pedidos, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validar
        $request->validate([
            "cliente_id" => "required",
            "productos" => "required"
        ]);

        // guardar pedido
        $cliente_id = $request->cliente_id;
        $productos = $request->productos;

        $pedido = new Pedido();
        $pedido->fecha = date("Y-m-d H:i:s");
        $pedido->cod_pedido = "COD_001";
        $pedido->estado = 1;
        $pedido->observacion = "SIN OBSERVACION";
        $pedido->cliente_id = $cliente_id;
        $pedido->save();

        // agreagr los productos al pedido
        foreach ($productos as $prod) {
            $prod_id = $prod["id"];
            $cantidad = $prod["cantidad"];
            
            $pedido->productos()->attach([$prod_id => ["cantidad" => $cantidad]]);
        }

        $pedido->estado = 2;
        $pedido->update();

        return response()->json(["message" => "Pedido registrado"], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
