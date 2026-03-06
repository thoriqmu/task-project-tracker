import { defineStore } from 'pinia';
import api from '../services/api';
import type { User } from '../types';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as User | null,
    token: null as string | null,
  }),
  actions: {
    setAuth(user: User, token: string) {
      this.user = user;
      this.token = token;
    },
    clearAuth() {
      this.user = null;
      this.token = null;
    },
    async logout() {
      try {
        await api.post('/auth/logout');
      } catch (e) {
        console.error('Logout error', e);
      } finally {
        this.clearAuth();
      }
    }
  },
  persist: true,
});
