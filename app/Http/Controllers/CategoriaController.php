<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $this->authorize("listar_categoria");

        $categorias = Categoria::get();

        return response()->json($categorias, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $this->authorize("guardar_categoria");
        // validar
        $request->validate([
            "nombre" => "required|unique:categorias"
        ]);
        // guardar
        $categoria = new Categoria();
        $categoria->nombre = $request->nombre;
        $categoria->detalle = $request->detalle;
        $categoria->save();

        return response()->json(["message" => "Categoria Registrada"], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // $this->authorize("mostrar_categoria");
        $categoria = Categoria::find($id);

        return response()->json($categoria, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // $this->authorize("modificar_categoria");
        // validar
        $request->validate([
            "nombre" => "required|unique:categorias,nombre,$id"
        ]);
        $categoria = Categoria::find($id);
        $categoria->nombre = $request->nombre;
        $categoria->detalle = $request->detalle;
        $categoria->update();

        return response()->json($categoria, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // $this->authorize("eliminar_categoria");

        $categoria = Categoria::find($id);
        $categoria->delete();
        return response()->json(["message" => "Categoria Eliminada"], 200);
    }
}
