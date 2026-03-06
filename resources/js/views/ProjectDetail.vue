<template>
  <div class="space-y-6 max-w-7xl mx-auto pb-10">
    <div v-if="projectStore.loading" class="text-center py-10 text-gray-500">
      Loading project details...
    </div>
    <div v-else-if="projectStore.error" class="text-center py-10 text-red-500 flex flex-col items-center">
      <p>{{ projectStore.error }}</p>
      <router-link to="/projects" class="mt-4 text-blue-600 hover:underline">Back to Projects</router-link>
    </div>
    
    <template v-else-if="project">
      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
          <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              {{ project.name }}
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
              {{ project.description }}
            </p>
          </div>
          <div class="flex items-center space-x-4">
            <span :class="[
              project.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800',
              'px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full'
            ]">
              {{ project.status }}
            </span>
            <button
              @click="openEditModal"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Edit Project
            </button>
          </div>
        </div>
      </div>

      <!-- Kanban Board for Tasks -->
      <div class="mt-8">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold text-gray-900">Project Tasks</h2>
          <button
            @click="openAddTaskModal"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Add Task
          </button>
        </div>

        <div class="flex overflow-x-auto space-x-6 pb-4 -mx-4 px-4 sm:mx-0 sm:px-0">
          <!-- Columns -->
          <div v-for="category in categories" :key="category.id" class="flex-shrink-0 w-80 bg-gray-100 rounded-lg p-3 flex flex-col h-full min-h-[500px]">
            <h3 class="font-medium text-gray-900 mb-3 px-1">{{ category.name }}</h3>
            <div 
              class="flex-1 space-y-3 p-1 min-h-[100px] rounded-md transition-colors"
              @dragover.prevent
              @dragenter.prevent
              @drop="onDrop($event, category.id)"
              :class="{'bg-gray-200': draggingOver === category.id}"
            >
              <div 
                v-for="task in getTasksByCategory(category.id)" 
                :key="task.id"
                draggable="true"
                @dragstart="onDragStart($event, task)"
                @dragend="onDragEnd"
                class="bg-white p-4 rounded shadow-sm border border-gray-200 cursor-move hover:shadow-md transition-shadow relative group"
              >
                <div class="flex justify-between items-start mb-2">
                  <h4 class="font-medium text-gray-900 text-sm truncate pr-6" :title="task.title">{{ task.title }}</h4>
                  <div class="absolute top-3 right-2 opacity-0 group-hover:opacity-100 transition-opacity flex space-x-1">
                    <button @click.stop="openEditTaskModal(task)" class="text-gray-400 hover:text-blue-600">
                      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                      </svg>
                    </button>
                    <button @click.stop="confirmDeleteTask(task)" class="text-gray-400 hover:text-red-600">
                      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </div>
                <p class="text-xs text-gray-500 line-clamp-2 mb-3">{{ task.description }}</p>
                <div class="text-xs font-medium" :class="isOverdue(task.due_date) ? 'text-red-600' : 'text-gray-500'">
                  Due: {{ new Date(task.due_date).toLocaleDateString() }}
                </div>
              </div>
              <div v-if="getTasksByCategory(category.id).length === 0" class="text-xs text-gray-400 text-center py-4 border-2 border-dashed border-gray-300 rounded-md">
                Drop tasks here
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Modals -->
    <!-- Edit Project Modal -->
    <Modal :isOpen="isEditProjectOpen" title="Edit Project" @close="isEditProjectOpen = false">
      <form @submit.prevent="submitEditProject" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <input type="text" v-model="projectForm.name" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border py-2 px-3">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Description</label>
          <textarea v-model="projectForm.description" rows="3" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border border-gray-300 rounded-md py-2 px-3"></textarea>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Status</label>
          <select v-model="projectForm.status" class="mt-1 block w-full pl-3 pr-10 py-2 border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md shadow-sm border">
            <option value="active">Active</option>
            <option value="archived">Archived</option>
          </select>
        </div>
        <div v-if="projectFormError" class="text-red-500 text-sm">{{ projectFormError }}</div>
        <div class="mt-5 sm:flex sm:flex-row-reverse gap-2">
            <button type="submit" :disabled="submittingProject" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:w-auto sm:text-sm disabled:opacity-50">
              Save
            </button>
            <button type="button" @click="isEditProjectOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 sm:mt-0 sm:w-auto sm:text-sm">
              Cancel
            </button>
        </div>
      </form>
      <template #footer><span></span></template>
    </Modal>

    <!-- Add/Edit Task Modal -->
    <Modal :isOpen="isTaskModalOpen" :title="isEditTaskMode ? 'Edit Task' : 'Add Task'" @close="isTaskModalOpen = false">
      <form @submit.prevent="submitTaskForm" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Title</label>
          <input type="text" v-model="taskForm.title" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-2 px-3 border">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Description</label>
          <textarea v-model="taskForm.description" rows="3" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border border-gray-300 rounded-md py-2 px-3"></textarea>
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
            <input type="date" v-model="taskForm.due_date" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border py-2 px-3">
          </div>
        </div>
        <div v-if="taskFormError" class="text-red-500 text-sm">{{ taskFormError }}</div>
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

    <!-- Delete Confirmation -->
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
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useProjectStore } from '../stores/projectStore';
import { useTaskStore } from '../stores/taskStore';
import Modal from '../components/Modal.vue';
import type { Task } from '../types';

const route = useRoute();
const projectStore = useProjectStore();
const taskStore = useTaskStore();

const projectId = route.params.id as string;
const project = computed(() => projectStore.currentProject);

// Fixed categories (from seeder ID map)
const categories = [
  { id: 1, name: 'Todo' },
  { id: 2, name: 'InProgress' },
  { id: 3, name: 'Testing' },
  { id: 4, name: 'Done' },
  { id: 5, name: 'Pending' }
];

const loadProject = async () => {
  await projectStore.fetchProject(projectId);
};

// Computed tasks directly from the nested project response
const projectTasks = computed(() => project.value?.tasks || []);

const getTasksByCategory = (categoryId: number) => {
  return projectTasks.value.filter(t => t.category_id === categoryId);
};

const isOverdue = (dateString: string) => {
  return new Date(dateString) < new Date(new Date().setHours(0,0,0,0));
};

// Drag & Drop Handlers
const draggingOver = ref<number | null>(null);

const onDragStart = (e: DragEvent, task: Task) => {
  if (e.dataTransfer) {
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('task_id', task.id.toString());
  }
};

const onDragEnd = () => {
  draggingOver.value = null;
};

const onDrop = async (e: DragEvent, categoryId: number) => {
  draggingOver.value = null;
  const taskId = e.dataTransfer?.getData('task_id');
  if (taskId) {
    const task = projectTasks.value.find(t => t.id === Number(taskId));
    if (task && task.category_id !== categoryId) {
      // Optimistic update locally
      const oldCat = task.category_id;
      task.category_id = categoryId;
      try {
        await taskStore.updateTask(task.id, { category_id: categoryId });
      } catch (err) {
        // Revert on failure
        task.category_id = oldCat;
        alert('Gagal memindahkan task');
      }
    }
  }
};

// Edit Project Modal
const isEditProjectOpen = ref(false);
const submittingProject = ref(false);
const projectFormError = ref('');
const projectForm = ref({ name: '', description: '', status: '' });

const openEditModal = () => {
  if (project.value) {
    projectForm.value = {
      name: project.value.name,
      description: project.value.description,
      status: project.value.status
    };
    isEditProjectOpen.value = true;
  }
};

const submitEditProject = async () => {
  submittingProject.value = true;
  projectFormError.value = '';
  try {
    await projectStore.updateProject(projectId, projectForm.value);
    isEditProjectOpen.value = false;
  } catch (err: any) {
    projectFormError.value = err.message;
  } finally {
    submittingProject.value = false;
  }
};

// Add/Edit Task Modal
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
  project_id: Number(projectId)
});

const openAddTaskModal = () => {
  isEditTaskMode.value = false;
  taskForm.value = {
    title: '',
    description: '',
    category_id: 1,
    due_date: new Date().toISOString().split('T')[0],
    project_id: Number(projectId)
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
    project_id: Number(projectId)
  };
  taskFormError.value = '';
  isTaskModalOpen.value = true;
};

const submitTaskForm = async () => {
  submittingTask.value = true;
  taskFormError.value = '';
  try {
    if (isEditTaskMode.value && currentEditingTaskId.value) {
      await taskStore.updateTask(currentEditingTaskId.value, taskForm.value);
    } else {
      await taskStore.createTask(taskForm.value);
    }
    await loadProject(); // Refresh the whole details to get updated nested tasks securely
    isTaskModalOpen.value = false;
  } catch (err: any) {
    taskFormError.value = err.message;
  } finally {
    submittingTask.value = false;
  }
};

// Delete Task Modal
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
      await loadProject();
      isDeleteModalOpen.value = false;
    } catch (err: any) {
      alert(err.message);
    }
  }
};

onMounted(() => {
  loadProject();
});
</script>
