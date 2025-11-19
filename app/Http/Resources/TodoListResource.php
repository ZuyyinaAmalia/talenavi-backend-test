<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Resource untuk representasi JSON dari TodoList
class TodoListResource extends JsonResource
{
    // Mengubah model TodoList menjadi array untuk response
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'assignee' => $this->assignee,
            'due_date' => $this->due_date,
            'time_tracked' => $this->time_tracked,
            'status' => $this->status,
            'priority' => $this->priority,
        ];
    }
}
