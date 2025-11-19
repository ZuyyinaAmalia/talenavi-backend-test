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

        // Set nilai default waktu dan status jika tidak ada request
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

    // Melakukan filter pada data yang ingin diekspor
    public function exportExcel(Request $request) 
    {
        $query = TodoList::query();

        // Filter pada title untuk melakukan pencarian parsial
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        // Filter assignee agar bisa memilih beberapa assignee sekaligus
        if ($request->has('assignee')) {
            $assignees = explode(',', $request->assignee); 
            $query->whereIn('assignee', $assignees);
        }

        // Filter due date untuk menghasilkan data dalam rentang tanggal tertentu
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('due_date', [$request->start_date, $request->end_date]);
        }

        // Filter time tracked untuk menghasilkan data dalam rentang nilai tertentu
        if ($request->has('min_time') && $request->has('max_time')) {
            $query->whereBetween('time_tracked', [$request->min_time, $request->max_time]);
        }

        // Filter status agar bisa memilih beberapa status sekaligus 
        if ($request->has('status')) {
            $statuses = explode(',', $request->status);
            $query->whereIn('status', $statuses);
        }

        // Filter priority agar bisa memilih beberapa priority sekaligus
        if ($request->has('priority')) {
            $priorities = explode(',', $request->priority);
            $query->whereIn('priority', $priorities);
        }
 
        $data = $query->get();

        return Excel::download(new TodoListExport($data), 'todo_report.xlsx');
    }

}
