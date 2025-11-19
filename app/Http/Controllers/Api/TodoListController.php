<?php

namespace App\Http\Controllers\Api;

use App\Models\TodoList;
use App\Http\Resources\TodoListResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TodoListExport;

class TodoListController extends Controller
{
    // Endpoint untuk mendapatkan semua todo list
    public function index() {
        $todoLists = TodoList::get();

        if($todoLists->count() > 0){
            return TodoListResource::collection($todoLists);
        } else {
            return response()->json(['message' => 'No record available', 200]);
        }
    }

    // Menyimpan todolist baru
    public function store(Request $request) {
        // Validasi input request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'assignee' => 'nullable|string',
            'due_date' => 'required|date|after_or_equal:today',
            'time_tracked' => 'numeric',
            'status' => 'in:pending,open,in_progress,completed',
            'priority' => 'required|in:low,medium,high'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages(),
            ], 422);
        }

        // Nilai default jika tidak diberi pada request
        $time_tracked = $request->time_tracked ?? 0;
        $status = $request->status ?? 'pending';

        $todoList = TodoList::create([
            'title' => $request->title,
            'assignee' => $request->assignee,
            'due_date' => $request->due_date,
            'time_tracked' => $time_tracked,
            'status' => $status,
            'priority' => $request->priority,
        ]);

        return response()->json(new TodoListResource($todoList), 200);
    }

    // Mengekspor todo list ke excel dengan filter opsional
    public function exportExcel(Request $request) 
    {
        $query = TodoList::query();

        // Filter partial match
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        // Filter assignee (multiple strings)
        if ($request->has('assignee')) {
            $assignees = explode(',', $request->assignee); 
            $query->whereIn('assignee', $assignees);
        }

        // Filter due date (range) 
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('due_date', [$request->start_date, $request->end_date]);
        }

        // Filter time tracked (range)
        if ($request->has('min_time') && $request->has('max_time')) {
            $query->whereBetween('time_tracked', [$request->min_time, $request->max_time]);
        }

        // Filter status (multiple strings) 
        if ($request->has('status')) {
            $statuses = explode(',', $request->status);
            $query->whereIn('status', $statuses);
        }

        // Filter priority (multiple strings) 
        if ($request->has('priority')) {
            $priorities = explode(',', $request->priority);
            $query->whereIn('priority', $priorities);
        }
 
        $data = $query->get();

        return Excel::download(new TodoListExport($data), 'todo_report.xlsx');
    }

}
