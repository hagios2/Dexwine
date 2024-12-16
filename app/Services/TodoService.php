<?php

namespace App\Services;

use App\Http\Requests\TodoRequest;
use App\Http\Resources\TodoCollection;
use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodoService
{
    public function index(Request $request): TodoCollection
    {
        $query = Todo::query();
        $limit = $request->limit ?? 15;

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('keyword')) {
            $query->where(fn($query) => [
                $query->where('title', 'like', "%{$request->keyword}%")
                    ->orWhere('details', 'like', "%{$request->keyword}%")
                ]
            );
        }

        $todos = $query->paginate($limit);

        return new TodoCollection($todos);
    }

    public function store(TodoRequest $request): JsonResponse
    {
        $todo = Todo::create($request->validated());

        return response()->json([
            'message' => 'Created todo successfully',
            'data' => new TodoResource($todo->refresh())
        ], 201);
    }

    public function show(Todo $todo): JsonResponse
    {
        return response()->json([
            'message' => 'Fetched Todo successfully',
            'data' => new TodoResource($todo)
        ]);
    }

    public function update(Todo $todo, TodoRequest $request): JsonResponse
    {
        $todo->update($request->validated());

        return response()->json([
            'message' => 'Todo updated successfully',
            'data' => new TodoResource($todo->refresh())
        ]);
    }

    public function destroy(Todo $todo): JsonResponse
    {
        $todo->delete();

        return response()->json([
            'message' => 'Todo deleted successfully',
            'data' => []
        ]);
    }
}
