# Catatan Penggunaan AI (AI Usage Documentation)

Dokumen ini berisi daftar *prompt*, model AI, serta *tools* yang digunakan selama proses pengembangan aplikasi **Task & Project Tracker**. Hal ini ditulis untuk menjaga transparansi penggunaan AI (seperti Antigravity, Claude, Gemini) dalam *code generation* dan *problem solving*.

---

## 1. Tahap 1: Setup Database & Migrasi
- **AI / Agent:** Antigravity (Model: Claude Sonnet 4.6)
- **Tool / MCP yang diakses AI:** `run_command` (Artisan CLI), `write_to_file`

**Prompt:**
> In this project, you will be tasked with assisting me in creating a task and project tracker. I've included all the commands in the TASK.md file. First, create a migration. the migration is as follows:
> 
> users:
> 1. id (bigint) pk
> 2. name (string)
> 3. email (string) unique
> 4. password (string)
> 5. is_admin (boolean) defaults to false
> 6. email_verified_at (timestamp)
> 7. created_at (timestamp)
> 8. updated_at (timestamp)
> 
> personal_access_tokens
> 1. id (bigint) pk
> 2. tokenable_id (bigint) fk
> 3. tokenable_type (string)
> 4. name (string)
> 5. unique token (string).
> 6. abilities (text)
> 7. last_used_at (timestamp)
> 8. expires_at (timestamp)
> 9. created_at (timestamp)
> 10. updated_at (timestamp)
> 
> projects
> 1. id (bigint) pk
> 2. created_by (bigint) fk
> 3. name (strings)
> 4. description (text)
> 5. status (enum [active|archived])
> 6. created_at (timestamp)
> 7. updated_at (timestamp)
> 
> categories
> 1. id (bigint) pk
> 2. name (string [Todo|InProgress|Testing|Done|Pending)
> 3. created_at (timestamp)
> 4. updated_at (timestamp)
> 
> tasks
> 1. id (bigint) pk
> 2. project_id (bigint) fk
> 3. category_id (bigint) fk
> 4. created_by (bigint) fk
> 5. deleted_by (bigint) fk nullable
> 6. title (string)
> 7. description (text)
> 8. due_date (date)
> 9. deleted_at (timestamp) nullable
> 10. created_at (timestamp)
> 11. updated_at (timestamp)

---

## 2. Tahap 2: Pembuatan Model Eloquent
- **AI / Agent:** Antigravity (Model: Claude Sonnet 4.6)
- **Tool / MCP yang diakses AI:** `multi_replace_file_content`, `view_file`

**Prompt:**
> Next, from the migration that I have created, I have created the model. Change the Category.php, Project.php, Task.php files according to the migration that was previously created

---

## 3. Tahap 3: Pembuatan Backend REST API
- **AI / Agent:** Antigravity (Model: Claude Sonnet 4.6)
- **Tool / MCP yang diakses AI:** `run_command` (Artisan Make Controllers & Requests), `write_to_file`

**Prompt:**
> Next, create the backend API.
> 1. Authentication
> During authentication, users will be prompted to log in using their email and password and receive a personal access token. This personal access token is then used to log in. Logging out will delete the active token.
> 2. Project Management
> You can create a new project with mandatory inputs: name, description, and status (active/archived). You can also edit the name, description, or status. To get all projects, you'll retrieve data for all projects, then add the query parameters name (to search by name) or status (to filter by status). This project contains project details. If you get project details, you'll retrieve data for this project and an array of tasks within this project. No soft delete is required for project management.
> 3. Task Management
> You can create a new task with mandatory inputs: title, description, category, deadline, and associated project. You can also edit a task by editing the title, description, category, deadline, and associated project. To get all tasks, you'll retrieve data for all tasks, then add the query parameters title (to search for task title), category (to filter by category), and project (to filter by project). Then, you can implement a soft delete for this task.
> 4. Dashboard
> Get data on total active projects, uncompleted tasks, and a list of tasks approaching their due date.
> 
> All of this is detailed in the API response status and description, not just a 500 error. When creating the controller method, use try-catch to ensure it's neat.

---

## 4. Tahap 4: Implementasi Frontend (Vue 3 + TypeScript)
- **AI / Agent:** Gemini 3.1 Pro (via Antigravity)
- **Tool / MCP yang diakses AI:** `run_command` (NPM install Vite/Vue/Pinia/Tailwind), `write_to_file` (Vue SFC Components)

**Prompt:**
> Now for the frontend implementation, let's revisit TASK.md. This frontend uses Vue.js 3 and Typescript, with the same frontend folder structure as TASK.md. The pages are:
> 1. Login
> The login page is where users enter their email and password. After successfully obtaining a token, they can access the next page.
> 2. Next, you'll enter the main page, which consists of the Dashboard, Project, and Task. The sidebar contains the website name, TaskTracker, followed by the menus, Dashboard, Project, and Task. The top bar contains the user in the upper right corner and a logout button.
> 2. Dashboard
> The dashboard page contains two cards: the number of active projects and the number of uncompleted tasks. Below that, there are tasks approaching their due date, with information about the task name, project name, and a countdown to the deadline (if the deadline is exceeded, a message will appear).
> 3. Project page
> The project page contains information about all projects. The page title is at the top, followed by an "Add Project" button to the right. Below that, there's a search bar and a filter based on status. Below that is a project table containing the project name, description, status, tasks (number of tasks), creation date, and actions (details and edit). Clicking "Add Project" displays an "Add New Project" overlay with project name, description, and a status dropdown (active/archived) field, with cancel and save buttons below it. Clicking "Edit" in a project row displays an "Edit Project" overlay with project name, description, and a status dropdown (active/archived) field, with the project's value below it, with cancel and save buttons below it. Clicking "Detail" displays the project details page.
> 4. Project Detail Page
> The project details page contains the project name, description, creation date, total tasks, and total tasks completed (how many/how many). To the right is the project status, and to the right is the edit button (similar to the project edit overlay on the project page). Below that is a list of tasks in card format that can be moved between categories (each card contains the task name, deadline, and edit and delete buttons).
> 5. Task Page
> The task page contains information about all tasks. Above is the page title and to the right is the Add Task button. Below it is a search bar, category filter, and project filter. Below that is a table containing the task title, project name, category, deadline, and action (edit and delete buttons). When you click Add Task, an Add New Task overlay will appear with task title, description, category dropdown, due date (date format without time), and project dropdown fields, as well as cancel and save buttons. When you click edit task, the task row displays an Add New Task overlay with task title, description, category dropdown, due date (date format without time), and project dropdown fields with the task value, as well as cancel and save buttons. When you click edit, a pop-up will appear to confirm deletion.

---

## 5. Tahap 5: Bug Fixing (Task Counts, Relasi Response, & Modal UI)
- **AI / Agent:** Gemini 3.1 Pro (via Antigravity)
- **Tool / MCP yang diakses AI:** `view_file`, `multi_replace_file_content`

**Prompt:**
> In @ProjectList.vue, the task count is not retrieved even though there is data. Then, the add project and edit project overlays are also blank. Then, in @ProjectDetail.vue, no tasks appear according to those details. The add task and edit project overlays are also blank. In TaskList.vue, the project name and category are not displayed in the columns. The edit, add task, and delete overlays are also blank.

---

## 6. Tahap 6: Automated Unit Testing
- **AI / Agent:** Gemini 3.1 Pro (via Antigravity)
- **Tool / MCP yang diakses AI:** `run_command` (PHPUnit & Vitest CLI), `write_to_file`

**Prompt:**
> Next, I created a unit test, 1 for the backend using phpunit and 1 for the frontend using vitest
