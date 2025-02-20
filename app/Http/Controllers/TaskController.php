<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return TaskResource::collection($tasks);
    }


    public function store(TaskRequest $request)
    {
        $task = Task::create([
            'title' => $request->input('title'),
            'status' => $request->input('status', 'pending'),
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'message' => __('task.store_success'),
            'data' => new TaskResource($task)
        ], 201);
    }

    public function update(TaskRequest $request, Task $task)
    {
        abort_unless($task->user_id === auth()->id(), 403);

        $task->update([
            'title' => $request->input('title'),
            'status' => $request->input('status', $task->status),
        ]);

        return response()->json([
            'message' => __('task.update_success'),
            'data' => new TaskResource($task)
        ], 200);
    }

    public function destroy(Task $task)
    {
        abort_unless($task->user_id === auth()->id(), 403);
        $task->delete();
        return response()->json([
            'message' => __('task.destroy_success'),
        ], 204);
    }
}
