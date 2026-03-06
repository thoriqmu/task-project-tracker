<template>
  <div class="h-full w-64 bg-white border-r border-gray-200 flex flex-col hidden md:flex">
    <div class="h-16 flex items-center px-6 border-b border-gray-200">
      <h1 class="text-xl font-bold text-gray-800 tracking-tight">TaskTracker</h1>
    </div>
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
      <router-link
        v-for="item in navigation"
        :key="item.name"
        :to="{ name: item.name }"
        v-slot="{ isActive, navigate, href }"
        custom
      >
        <a
          :href="href"
          @click="navigate"
          :class="[
            isActive
              ? 'bg-blue-50 text-blue-700'
              : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900',
            'group flex items-center px-3 py-2 text-sm font-medium rounded-md'
          ]"
        >
          <span class="truncate">{{ item.label }}</span>
        </a>
      </router-link>
    </nav>
  </div>
</template>

<script setup lang="ts">
const navigation = [
  { name: 'Dashboard', label: 'Dashboard' },
  { name: 'Projects', label: 'Project' },
  { name: 'Tasks', label: 'Task' },
];
</script>
