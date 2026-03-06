<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * GET ALL PROJECT
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Project::with('creator')->withCount(['tasks' => function($q) {
                $q->whereNull('deleted_at');
            }]);

            if ($request->filled('name')) {
                $query->where('name', 'ILIKE', '%' . $request->name . '%');
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $projects = $query->latest()->get();

            $data = $projects->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'description' => $project->description,
                    'created_by' => $project->creator->name,
                    'status' => $project->status,
                    'created_at' => $project->created_at,
                    'updated_at' => $project->updated_at,
                    'tasks_count' => $project->tasks_count,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data project berhasil diambil.',
                'data'    => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server saat mengambil data project.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * CREATE PROJECT
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        try {
            $project = Project::create([
                'created_by'  => auth()->id(),
                'name'        => $request->name,
                'description' => $request->description,
                'status'      => $request->status,
            ]);

            $data = [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'created_by' => $project->creator->name,
                'status' => $project->status,
                'created_at' => $project->created_at,
                'updated_at' => $project->updated_at,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Project berhasil dibuat.',
                'data'    => $data,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server saat membuat project.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET PROJECT BY ID
     */
    public function show(int $id): JsonResponse
    {
        try {
            $project = Project::with([
                'creator',
                'tasks' => fn ($q) => $q->whereNull('deleted_at')
                                        ->with(['category', 'creator']),
            ])->find($id);

            $data = [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'created_by' => $project->creator->name,
                'status' => $project->status,
                'created_at' => $project->created_at,
                'updated_at' => $project->updated_at,
                'tasks' => [],
            ];

            foreach ($project->tasks as $task) {
                $data['tasks'][] = [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'due_date' => $task->due_date,
                    'category_id' => $task->category_id,
                    'project_id' => $task->project_id,
                    'status' => $task->category->name,
                    'created_at' => $task->created_at,
                    'updated_at' => $task->updated_at,
                ];
            }

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail project berhasil diambil.',
                'data'    => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server saat mengambil detail project.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * UPDATE PROJECT
     */
    public function update(UpdateProjectRequest $request, int $id): JsonResponse
    {
        try {
            $project = Project::find($id);

            $data = [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'created_by' => $project->creator->name,
                'status' => $project->status,
                'created_at' => $project->created_at,
                'updated_at' => $project->updated_at,
            ];

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project tidak ditemukan.',
                ], 404);
            }

            $project->update($request->only(['name', 'description', 'status']));

            return response()->json([
                'success' => true,
                'message' => 'Project berhasil diperbarui.',
                'data'    => $project->fresh('creator'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server saat memperbarui project.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
