<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
    
    <div v-if="loading" class="text-gray-500">Loading dashboard...</div>
    <div v-else-if="error" class="text-red-500">{{ error }}</div>
    
    <div v-else>
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate">
              Active Projects
            </dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">
              {{ stats.total_active_projects }}
            </dd>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate">
              Uncompleted Tasks
            </dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">
              {{ stats.uncompleted_tasks }}
            </dd>
          </div>
        </div>
      </div>

      <!-- Upcoming Tasks Table -->
      <div class="mt-8 flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
              <h2 class="px-6 py-4 text-lg font-medium text-gray-900 bg-white border-b border-gray-200">
                Tasks Approaching Due Date (Next 7 days)
              </h2>
              <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Task Name</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Project</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Due Date</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                  <tr v-for="task in stats.upcoming_tasks" :key="task.id" :class="isOverdue(task.due_date) ? 'bg-red-50' : ''">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                      {{ task.title }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                      {{ task.project?.name || '-' }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                      {{ new Date(task.due_date).toLocaleDateString() }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                      <span v-if="isOverdue(task.due_date)" class="text-red-600 font-semibold">Overdue</span>
                      <span v-else class="text-orange-600">{{ countdown(task.due_date) }}</span>
                    </td>
                  </tr>
                  <tr v-if="!stats.upcoming_tasks || stats.upcoming_tasks.length === 0">
                    <td colspan="4" class="py-4 text-center text-sm text-gray-500">No upcoming tasks.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import api from '../services/api';

const loading = ref(true);
const error = ref('');
const stats = ref({
  total_active_projects: 0,
  uncompleted_tasks: 0,
  upcoming_tasks: [] as any[]
});

const loadDashboard = async () => {
  try {
    const { data } = await api.get('/dashboard');
    if (data.success) {
      stats.value = data.data;
    }
  } catch (err: any) {
    error.value = 'Halaman Dashboard Gagal dimuat';
  } finally {
    loading.value = false;
  }
};

const isOverdue = (dateString: string) => {
  return new Date(dateString) < new Date(new Date().setHours(0,0,0,0));
};

const countdown = (dateString: string) => {
  const diffTime = Math.abs(new Date(dateString).getTime() - new Date().getTime());
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return `${diffDays} hari lagi`;
};

onMounted(() => {
  loadDashboard();
});
</script>
