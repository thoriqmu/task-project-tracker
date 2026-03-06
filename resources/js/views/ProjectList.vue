<template>
  <div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
      <h1 class="text-2xl font-semibold text-gray-900">Projects</h1>
      <div class="mt-4 sm:mt-0">
        <button 
          @click="openAddModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Add Project
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-md shadow flex gap-4 items-center">
      <div class="w-1/3">
        <label for="search" class="sr-only">Search Name</label>
        <input 
          v-model="filters.name" 
          type="text" 
          name="search" 
          id="search" 
          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-3 border" 
          placeholder="Search by project name..."
        >
      </div>
      <div class="w-1/4">
        <select 
          v-model="filters.status" 
          class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
        >
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="archived">Archived</option>
        </select>
      </div>
      <div>
        <button 
          @click="fetchData" 
          class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
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
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tasks Count</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                  <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-if="projectStore.loading">
                  <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Loading projects...</td>
                </tr>
                <tr v-else-if="projectStore.projects.length === 0">
                  <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No projects found.</td>
                </tr>
                <tr v-for="project in projectStore.projects" :key="project.id" v-else>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ project.name }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="[
                      project.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800',
                      'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
                    ]">
                      {{ project.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ project.tasks_count || 0 }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ new Date(project.created_at).toLocaleDateString() }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <router-link :to="{ name: 'ProjectDetail', params: { id: project.id } }" class="text-blue-600 hover:text-blue-900 mr-4">Details</router-link>
                    <button @click="openEditModal(project)" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Form -->
    <Modal 
      :isOpen="isModalOpen" 
      :title="isEditMode ? 'Edit Project' : 'Add Project'" 
      @close="closeModal" 
    >
      <form @submit.prevent="submitForm" class="space-y-4">
        <div>
          <label for="projectName" class="block text-sm font-medium text-gray-700">Name</label>
          <input type="text" v-model="form.name" id="projectName" required class="mt-1 flex-1 focus:ring-blue-500 focus:border-blue-500 block w-full border-gray-300 rounded-md sm:text-sm py-2 px-3 border shadow-sm">
        </div>
        <div>
          <label for="projectDesc" class="block text-sm font-medium text-gray-700">Description</label>
          <textarea v-model="form.description" id="projectDesc" rows="3" required class="mt-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md py-2 px-3"></textarea>
        </div>
        <div>
          <label for="projectStatus" class="block text-sm font-medium text-gray-700">Status</label>
          <select v-model="form.status" id="projectStatus" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md shadow-sm border">
            <option value="active">Active</option>
            <option value="archived">Archived</option>
          </select>
        </div>
        
        <div v-if="formError" class="text-red-500 text-sm py-2">{{ formError }}</div>

        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-2">
            <button 
              type="submit" 
              :disabled="submitting"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              {{ submitting ? 'Saving...' : 'Save' }}
            </button>
            <button 
              type="button" 
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm"
              @click="closeModal"
            >
              Cancel
            </button>
        </div>
      </form>
      <template #footer><span></span></template>
    </Modal>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useProjectStore } from '../stores/projectStore';
import Modal from '../components/Modal.vue';
import type { Project } from '../types';

const projectStore = useProjectStore();

const filters = ref({
  name: '',
  status: ''
});

const isModalOpen = ref(false);
const isEditMode = ref(false);
const submitting = ref(false);
const formError = ref('');
const currentEditId = ref<number | null>(null);

const form = ref({
  name: '',
  description: '',
  status: 'active'
});

const fetchData = () => {
  projectStore.fetchProjects({
    name: filters.value.name || undefined,
    status: filters.value.status || undefined
  });
};

const openAddModal = () => {
  isEditMode.value = false;
  currentEditId.value = null;
  form.value = { name: '', description: '', status: 'active' };
  formError.value = '';
  isModalOpen.value = true;
};

const openEditModal = (project: Project) => {
  isEditMode.value = true;
  currentEditId.value = project.id;
  form.value = { 
    name: project.name, 
    description: project.description, 
    status: project.status 
  };
  formError.value = '';
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
};

const submitForm = async () => {
  submitting.value = true;
  formError.value = '';
  try {
    if (isEditMode.value && currentEditId.value) {
      await projectStore.updateProject(currentEditId.value, form.value);
    } else {
      await projectStore.createProject(form.value);
    }
    fetchData();
    closeModal();
  } catch (e: any) {
    formError.value = e.message;
  } finally {
    submitting.value = false;
  }
};

onMounted(() => {
  fetchData();
});
</script>
