<?php

namespace App\Http\Controllers;

use App\Http\Requests\TodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Http\Resources\TodoCollection;
use App\Models\Todo;
use App\Services\TodoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function __construct(protected TodoService $service)
    {
    }

    public function index(Request $request): TodoCollection
    {
        return $this->service->index($request);
    }

    public function store(TodoRequest $request): JsonResponse
    {
        return $this->service->store($request);
    }

    public function show(Todo $todo): JsonResponse
    {
        return $this->service->show($todo);
    }

    public function update(TodoRequest $request, Todo $todo): JsonResponse
    {
        return $this->service->update($todo, $request);
    }

    public function destroy(Todo $todo): JsonResponse
    {
        return $this->service->destroy($todo);
    }
}
