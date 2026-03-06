<template>
  <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
    <div class="flex-1"></div>
    <div class="flex items-center space-x-4">
      <div v-if="authStore.user" class="text-sm font-medium text-gray-700">
        {{ authStore.user.name }}
      </div>
      <button
        @click="authStore.logout()"
        class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
      >
        Logout
      </button>
    </div>
  </header>
</template>

<script setup lang="ts">
import { useAuthStore } from '../stores/authStore';
const authStore = useAuthStore();
</script>
