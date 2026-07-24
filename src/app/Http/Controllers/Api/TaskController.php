<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Listar todas las tareas con sus relaciones asignadas
    public function index()
    {
        $tareas = Task::with(['category', 'user'])->get();
        return response()->json($tareas, 200);
    }

    // Crear una tarea
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'fecha_limite' => 'nullable|date|after:today',
            'category_id' => 'required|exists:categories,id',
        ]);

        $tarea = Task::create([
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'],
            'fecha_limite' => $validated['fecha_limite'] ?? null,
            'category_id' => $validated['category_id'],
            'user_id' => auth()->id(),
            'estado' => 'pendiente',
        ]);

        return response()->json($tarea, 201);
    }

    // Mostrar una tarea específica
    public function show($id)
    {
        try {
            $task = Task::with(['category', 'user'])->findOrFail($id);
            return response()->json($task, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Tarea no encontrada'], 404);
        }
    }

    // Actualizar una tarea
    public function update(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);

            $validated = $request->validate([
                'titulo' => 'sometimes|string|max:150',
                'descripcion' => 'nullable|string',
                'fecha_limite' => 'nullable|date|after:today',
                'category_id' => 'sometimes|exists:categories,id',
                'estado' => 'sometimes|in:pendiente,en_progreso,completada',
            ]);

            $user = auth()->user();
            $isAdmin = $user && $user->rol === 'admin';
            if (!$isAdmin && $user->id !== $task->user_id) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $task->update($validated);
            return response()->json($task->fresh()->load(['category', 'user']), 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Tarea no encontrada'], 404);
        }
    }

    // Eliminar una tarea
    public function destroy($id)
    {
        try {
            $task = Task::findOrFail($id);

            $user = auth()->user();
            $isAdmin = $user && $user->rol === 'admin';
            if (!$isAdmin && $user->id !== $task->user_id) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $task->delete();
            return response()->json(null, 204);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Tarea no encontrada'], 404);
        }
    }
}