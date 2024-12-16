<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TodoCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'message' => 'fetched todo list',
            'data' => $this->collection->transform(function ($todo) {
                return [
                    'id' => $todo->id,
                    'title' => $todo->title,
                    'status' => $todo->status,
                    'details' => $todo->details,
                    'created_at' => $todo->created_at->format('D, d F Y')
                ];
            }),
            'links' => [
                'self' => 'link-value',
            ]
        ];
    }
}
