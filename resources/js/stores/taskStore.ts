import { defineStore } from 'pinia';
import api from '../services/api';
import type { Task, ApiResponse } from '../types';

export const useTaskStore = defineStore('task', {
  state: () => ({
    tasks: [] as Task[],
    currentTask: null as Task | null,
    loading: false,
    error: null as string | null,
  }),
  actions: {
    async fetchTasks(params?: { title?: string; category?: number; project?: number }) {
      this.loading = true;
      this.error = null;
      try {
        const { data } = await api.get<ApiResponse<Task[]>>('/tasks', { params });
        if (data.success && data.data) {
          this.tasks = data.data;
        }
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Failed to fetch tasks';
      } finally {
        this.loading = false;
      }
    },

    async createTask(payload: { title: string; description: string; category_id: number; due_date: string; project_id: number }) {
      try {
        const { data } = await api.post<ApiResponse<Task>>('/tasks', payload);
        if (data.success && data.data) {
          this.tasks.unshift(data.data);
          return data;
        }
      } catch (err: any) {
        throw new Error(err.response?.data?.message || 'Failed to create task');
      }
    },

    async updateTask(id: number | string, payload: Partial<{ title: string; description: string; category_id: number; due_date: string; project_id: number }>) {
      try {
        const { data } = await api.put<ApiResponse<Task>>(`/tasks/${id}`, payload);
        if (data.success && data.data) {
          const index = this.tasks.findIndex(t => t.id === Number(id));
          if (index !== -1) {
            this.tasks[index] = data.data;
          }
          return data;
        }
      } catch (err: any) {
        throw new Error(err.response?.data?.message || 'Failed to update task');
      }
    },

    async deleteTask(id: number | string) {
      try {
        const { data } = await api.delete<ApiResponse<null>>(`/tasks/${id}`);
        if (data.success) {
          this.tasks = this.tasks.filter(t => t.id !== Number(id));
          return true;
        }
      } catch (err: any) {
        throw new Error(err.response?.data?.message || 'Failed to delete task');
      }
    }
  }
});
