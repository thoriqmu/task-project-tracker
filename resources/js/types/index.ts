export interface User {
  id: number;
  name: string;
  email: string;
  is_admin: boolean;
}

export interface Project {
  id: number;
  name: string;
  description: string;
  status: 'active' | 'archived';
  created_at: string;
  tasks_count?: number;
  tasks?: Task[];
}

export interface Category {
  id: number;
  name: 'Todo' | 'InProgress' | 'Testing' | 'Done' | 'Pending';
}

export interface Task {
  id: number;
  title: string;
  description: string;
  due_date: string;
  project_id: number;
  category_id: number;
  category?: Category;
  project?: Project;
}

export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data?: T;
  error?: any;
}
