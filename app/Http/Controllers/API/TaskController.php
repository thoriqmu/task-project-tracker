<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TaskController extends Controller
{
    /**
     * GET ALL TASK
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Task::whereNull('deleted_at')
                         ->with(['project', 'category', 'creator']);

            if ($request->filled('title')) {
                $query->where('title', 'ILIKE', '%' . $request->title . '%');
            }

            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            if ($request->filled('project')) {
                $query->where('project_id', $request->project);
            }

            $tasks = $query->latest()->get();
            
            $data = $tasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'project' => $task->project->name,
                    'category' => $task->category->name,
                    'created_by' => $task->creator->name,
                    'deleted_by' => $task->deletedBy?->name,
                    'title' => $task->title,
                    'description' => $task->description,
                    'due_date' => $task->due_date,
                    'created_at' => $task->created_at,
                    'updated_at' => $task->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data task berhasil diambil.',
                'data'    => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server saat mengambil data task.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * CREATE TASK
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        try {
            $task = Task::create([
                'project_id'  => $request->project_id,
                'category_id' => $request->category_id,
                'created_by'  => auth()->id(),
                'title'       => $request->title,
                'description' => $request->description,
                'due_date'    => $request->due_date,
            ]);
            
            $data = [
                'id' => $task->id,
                'project' => $task->project->name,
                'category' => $task->category->name,
                'created_by' => $task->creator->name,
                'deleted_by' => $task->deletedBy?->name,
                'title' => $task->title,
                'description' => $task->description,
                'due_date' => $task->due_date,
                'created_at' => $task->created_at,
                'updated_at' => $task->updated_at,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Task berhasil dibuat.',
                'data'    => $data,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server saat membuat task.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * UPDATE TASK
     */
    public function update(UpdateTaskRequest $request, int $id): JsonResponse
    {
        try {
            $task = Task::whereNull('deleted_at')->find($id);
            
            $data = [
                'id' => $task->id,
                'project' => $task->project->name,
                'category' => $task->category->name,
                'created_by' => $task->creator->name,
                'deleted_by' => $task->deletedBy?->name,
                'title' => $task->title,
                'description' => $task->description,
                'due_date' => $task->due_date,
                'created_at' => $task->created_at,
                'updated_at' => $task->updated_at,
            ];

            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task tidak ditemukan.',
                ], 404);
            }

            $task->update($request->only([
                'project_id',
                'category_id',
                'title',
                'description',
                'due_date',
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Task berhasil diperbarui.',
                'data'    => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server saat memperbarui task.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE TASK
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $task = Task::whereNull('deleted_at')->find($id);

            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task tidak ditemukan atau sudah dihapus.',
                ], 404);
            }

            $task->update([
                'deleted_at' => Carbon::now(),
                'deleted_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task berhasil dihapus.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server saat menghapus task.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
