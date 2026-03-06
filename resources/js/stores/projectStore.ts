import { defineStore } from 'pinia';
import api from '../services/api';
import type { Project, ApiResponse } from '../types';

export const useProjectStore = defineStore('project', {
  state: () => ({
    projects: [] as Project[],
    currentProject: null as Project | null,
    loading: false,
    error: null as string | null,
  }),
  actions: {
    async fetchProjects(params?: { name?: string; status?: string }) {
      this.loading = true;
      this.error = null;
      try {
        const { data } = await api.get<ApiResponse<Project[]>>('/projects', { params });
        if (data.success && data.data) {
          this.projects = data.data;
        }
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Failed to fetch projects';
      } finally {
        this.loading = false;
      }
    },
    
    async fetchProject(id: number | string) {
      this.loading = true;
      this.error = null;
      try {
        const { data } = await api.get<ApiResponse<Project>>(`/projects/${id}`);
        if (data.success && data.data) {
          this.currentProject = data.data;
          return this.currentProject;
        }
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Failed to fetch project';
      } finally {
        this.loading = false;
      }
    },

    async createProject(payload: { name: string; description: string; status: string }) {
      try {
        const { data } = await api.post<ApiResponse<Project>>('/projects', payload);
        if (data.success && data.data) {
          this.projects.unshift(data.data);
          return data;
        }
      } catch (err: any) {
        throw new Error(err.response?.data?.message || 'Failed to create project');
      }
    },

    async updateProject(id: number | string, payload: { name?: string; description?: string; status?: string }) {
      try {
        const { data } = await api.put<ApiResponse<Project>>(`/projects/${id}`, payload);
        if (data.success && data.data) {
          const index = this.projects.findIndex(p => p.id === Number(id));
          if (index !== -1) {
            this.projects[index] = data.data;
          }
          if (this.currentProject?.id === Number(id)) {
            this.currentProject = { ...this.currentProject, ...data.data };
          }
          return data;
        }
      } catch (err: any) {
        throw new Error(err.response?.data?.message || 'Failed to update project');
      }
    }
  }
});
