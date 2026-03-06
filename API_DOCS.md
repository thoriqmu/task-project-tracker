# Task & Project API Documentation

This document provides details about the API endpoints available in the **Task & Project Tracker** application, as extracted from the Postman collection.

**Base URL:** `http://127.0.0.1:8000/api`

---

## Important Note regarding Authentication
All endpoints *except* `Login` require a Bearer Token for authorization. 
Add the `Authorization` header with the value `Bearer {your_token}` to your requests. The token is obtained after a successful login.

---

## 🔐 1. Authentication

### 1.1 Login
Authenticate a user and retrieve a Personal Access Token.
- **Method:** `POST`
- **URL:** `/auth/login`
- **Body (JSON):**
  ```json
  {
      "email": "jane.smith@example.com",
      "password": "123"
  }
  ```

### 1.2 Logout
Log out the currently authenticated user and invalidate their token.
- **Method:** `POST`
- **URL:** `/auth/logout`

---

## 📊 2. Dashboard

### 2.1 Get Dashboard
Retrieve statistics for the dashboard, including total active projects, uncompleted tasks, and upcoming due tasks.
- **Method:** `GET`
- **URL:** `/dashboard`

---

## 🗂️ 3. Projects

### 3.1 Get All Projects
Retrieve a list of all projects. Supports filtering via query parameters.
- **Method:** `GET`
- **URL:** `/projects`
- **Query Parameters (Optional):**
  - `name`: Filter projects by name (e.g., `?name=project`)
  - `status`: Filter projects by status (e.g., `?status=archived`)

### 3.2 Create New Project
Create a new project.
- **Method:** `POST`
- **URL:** `/projects`
- **Body (JSON):**
  ```json
  {
      "name": "Project #2",
      "description": "Ini adalah project kedua",
      "status": "active"
  }
  ```

### 3.3 Get Project Detail
Retrieve details of a specific project, including its tasks.
- **Method:** `GET`
- **URL:** `/projects/{id}` *(Example: `/projects/2`)*

### 3.4 Update Project
Update an existing project's details.
- **Method:** `PUT`
- **URL:** `/projects/{id}` *(Example: `/projects/2`)*
- **Body (JSON):**
  ```json
  {
      "name": "Update Project #2",
      "description": "Ini adalah update project kedua",
      "status": "archived"
  }
  ```

---

## 📝 4. Tasks

### 4.1 Get All Tasks
Retrieve a list of all tasks. 
- **Method:** `GET`
- **URL:** `/tasks`

### 4.2 Create New Task
Create a new task within a project.
- **Method:** `POST`
- **URL:** `/tasks`
- **Body (JSON):**
  ```json
  {
      "project_id": 2,
      "category_id": 2,
      "title": "Task 1 di Project #2",
      "description": "Ini adalah task pertama dari project kedua",
      "due_date": "2026-03-07"
  }
  ```

### 4.3 Update Task
Update an existing task.
- **Method:** `PUT`
- **URL:** `/tasks/{id}` *(Example: `/tasks/2`)*
- **Body (JSON):**
  ```json
  {
      "project_id": 2,
      "category_id": 1,
      "title": "Update Task 1 Project #2",
      "description": "Ini adalah update task pertama dari project kedua",
      "due_date": "2026-03-08"
  }
  ```

### 4.4 Delete Task
Soft-delete an existing task.
- **Method:** `DELETE`
- **URL:** `/tasks/{id}` *(Example: `/tasks/1`)*
