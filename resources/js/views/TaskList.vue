<template>
  <div class="space-y-6 max-w-7xl mx-auto pb-10">
    <div class="sm:flex sm:items-center sm:justify-between">
      <h1 class="text-2xl font-semibold text-gray-900">All Tasks</h1>
      <div class="mt-4 sm:mt-0">
        <button 
          @click="openAddTaskModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Add Task
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-md shadow flex flex-col sm:flex-row gap-4 items-end sm:items-center">
      <div class="w-full sm:w-1/3">
        <label for="search" class="block text-sm font-medium text-gray-700 sr-only">Search</label>
        <input 
          v-model="filters.title" 
          type="text" 
          name="search" 
          id="search" 
          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-3 border" 
          placeholder="Search task title..."
        >
      </div>
      <div class="w-full sm:w-1/4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
        <select 
          v-model="filters.category_id" 
          class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
        >
          <option value="">All Categories</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
        </select>
      </div>
      <div class="w-full sm:w-1/4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
        <select 
          v-model="filters.project_id" 
          class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
        >
          <option value="">All Projects</option>
          <option v-for="proj in activeProjects" :key="proj.id" :value="proj.id">{{ proj.name }}</option>
        </select>
      </div>
      <div class="w-full sm:w-auto mt-4 sm:mt-0">
        <button 
          @click="fetchData" 
          class="w-full sm:w-auto inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Filter
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="flex flex-col">
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                  <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-if="taskStore.loading">
                  <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Loading tasks...</td>
                </tr>
                <tr v-else-if="taskStore.tasks.length === 0">
                  <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No tasks found.</td>
                </tr>
                <tr v-for="task in taskStore.tasks" :key="task.id" v-else>
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ task.title }}</div>
                    <div class="text-sm text-gray-500 truncate max-w-xs">{{ task.description }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ task.project?.name || '-' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                      {{ task.category?.name || '-' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span :class="isOverdue(task.due_date) ? 'text-red-600 font-bold' : 'text-gray-900'">
                      {{ new Date(task.due_date).toLocaleDateString() }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button @click="openEditTaskModal(task)" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</button>
                    <button @click="confirmDeleteTask(task)" class="text-red-600 hover:text-red-900">Delete</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Task Modal -->
    <Modal :isOpen="isTaskModalOpen" :title="isEditTaskMode ? 'Edit Task' : 'Add Task'" @close="isTaskModalOpen = false">
      <form @submit.prevent="submitTaskForm" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Project</label>
          <select v-model="taskForm.project_id" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            <option v-for="proj in activeProjects" :key="proj.id" :value="proj.id">{{ proj.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Title</label>
          <input type="text" v-model="taskForm.title" required class="mt-1 flex-1 focus:ring-blue-500 focus:border-blue-500 block w-full border-gray-300 rounded-md sm:text-sm py-2 px-3 border shadow-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Description</label>
          <textarea v-model="taskForm.description" rows="3" required class="mt-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md py-2 px-3"></textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Category</label>
            <select v-model="taskForm.category_id" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Due Date</label>
            <input type="date" v-model="taskForm.due_date" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-2 px-3 border">
          </div>
        </div>
        
        <div v-if="taskFormError" class="text-red-500 text-sm py-2">{{ taskFormError }}</div>

        <div class="mt-5 sm:flex sm:flex-row-reverse gap-2">
            <button type="submit" :disabled="submittingTask" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:w-auto sm:text-sm disabled:opacity-50">
              Save
            </button>
            <button type="button" @click="isTaskModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 sm:mt-0 sm:w-auto sm:text-sm">
              Cancel
            </button>
        </div>
      </form>
      <template #footer><span></span></template>
    </Modal>

    <!-- Delete Confirmation Modal -->
    <Modal :isOpen="isDeleteModalOpen" title="Delete Task" @close="isDeleteModalOpen = false" @confirm="deleteTask">
      <div class="mt-2">
        <p class="text-sm text-gray-500">Are you sure you want to delete the task "{{ taskToDelete?.title }}"? This action cannot be undone.</p>
      </div>
      <template #footer>
        <button type="button" @click="deleteTask" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-sm">
          Delete
        </button>
        <button type="button" @click="isDeleteModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
          Cancel
        </button>
      </template>
    </Modal>

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useTaskStore } from '../stores/taskStore';
import { useProjectStore } from '../stores/projectStore';
import Modal from '../components/Modal.vue';
import type { Task } from '../types';

const taskStore = useTaskStore();
const projectStore = useProjectStore();

const categories = [
  { id: 1, name: 'Todo' },
  { id: 2, name: 'InProgress' },
  { id: 3, name: 'Testing' },
  { id: 4, name: 'Done' },
  { id: 5, name: 'Pending' }
];

const activeProjects = computed(() => {
  return projectStore.projects.filter(p => p.status === 'active');
});

const filters = ref({
  title: '',
  category_id: '' as string | number,
  project_id: '' as string | number
});

const fetchData = () => {
  taskStore.fetchTasks({
    title: filters.value.title || undefined,
    category: filters.value.category_id ? Number(filters.value.category_id) : undefined,
    project: filters.value.project_id ? Number(filters.value.project_id) : undefined,
  });
};

const isOverdue = (dateString: string) => {
  return new Date(dateString) < new Date(new Date().setHours(0,0,0,0));
};

// Add/Edit Task
const isTaskModalOpen = ref(false);
const isEditTaskMode = ref(false);
const submittingTask = ref(false);
const taskFormError = ref('');
const currentEditingTaskId = ref<number | null>(null);

const taskForm = ref({
  title: '',
  description: '',
  category_id: 1,
  due_date: new Date().toISOString().split('T')[0],
  project_id: '' as string | number,
});

const openAddTaskModal = () => {
  isEditTaskMode.value = false;
  taskForm.value = {
    title: '',
    description: '',
    category_id: 1,
    due_date: new Date().toISOString().split('T')[0],
    project_id: activeProjects.value.length > 0 ? activeProjects.value[0].id : '',
  };
  taskFormError.value = '';
  isTaskModalOpen.value = true;
};

const openEditTaskModal = (task: Task) => {
  isEditTaskMode.value = true;
  currentEditingTaskId.value = task.id;
  taskForm.value = {
    title: task.title,
    description: task.description,
    category_id: task.category_id,
    due_date: new Date(task.due_date).toISOString().split('T')[0],
    project_id: task.project_id,
  };
  taskFormError.value = '';
  isTaskModalOpen.value = true;
};

const submitTaskForm = async () => {
  if (!taskForm.value.project_id) {
    taskFormError.value = 'Mohon pilih sebuah project';
    return;
  }
  
  submittingTask.value = true;
  taskFormError.value = '';
  
  const payload = {
    ...taskForm.value,
    project_id: Number(taskForm.value.project_id)
  };

  try {
    if (isEditTaskMode.value && currentEditingTaskId.value) {
      await taskStore.updateTask(currentEditingTaskId.value, payload);
    } else {
      await taskStore.createTask(payload);
    }
    fetchData(); // reload tasks to get fresh joined relations
    isTaskModalOpen.value = false;
  } catch (err: any) {
    taskFormError.value = err.message;
  } finally {
    submittingTask.value = false;
  }
};

// Delete Task
const isDeleteModalOpen = ref(false);
const taskToDelete = ref<Task | null>(null);

const confirmDeleteTask = (task: Task) => {
  taskToDelete.value = task;
  isDeleteModalOpen.value = true;
};

const deleteTask = async () => {
  if (taskToDelete.value) {
    try {
      await taskStore.deleteTask(taskToDelete.value.id);
      isDeleteModalOpen.value = false;
    } catch (err: any) {
      alert(err.message);
    }
  }
};

onMounted(() => {
  projectStore.fetchProjects(); // need this to populate project select dropdown
  fetchData();
});
</script>
