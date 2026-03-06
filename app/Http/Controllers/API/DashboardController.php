<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * GET /api/dashboard
     * Kembalikan: total_active_projects, uncompleted_tasks, upcoming_tasks
     */
    public function index(): JsonResponse
    {
        try {
            // Total project dengan status active
            $totalActiveProjects = Project::where('status', 'active')->count();

            // Ambil category ID untuk 'Done'
            $doneCategory = Category::where('name', 'Done')->first();

            // Task yang belum selesai (bukan kategori Done) dan belum di-soft delete
            $uncompletedTasks = Task::whereNull('deleted_at')
                ->when($doneCategory, fn ($q) => $q->where('category_id', '!=', $doneCategory->id))
                ->count();

            // Task yang mendekati deadline (7 hari ke depan), belum done, belum dihapus
            $today    = Carbon::today();
            $nextWeek = Carbon::today()->addDays(7);

            $upcomingTasks = Task::whereNull('deleted_at')
                ->when($doneCategory, fn ($q) => $q->where('category_id', '!=', $doneCategory->id))
                ->whereBetween('due_date', [$today, $nextWeek])
                ->with(['project', 'category'])
                ->orderBy('due_date')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data dashboard berhasil diambil.',
                'data'    => [
                    'total_active_projects' => $totalActiveProjects,
                    'uncompleted_tasks'     => $uncompletedTasks,
                    'upcoming_tasks'        => $upcomingTasks,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server saat mengambil data dashboard.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
