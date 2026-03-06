# 🎯 Project Overview
Building a Task & Project Tracker application. This application allows users to manage projects and tasks within them, using categories as task status indicators.
**IMPORTANT:** This project uses a monorepo concept, meaning the API (Backend) and Frontend must be in the same repository.

# 🛠️ Tech Stack & Environment
- **Backend API:** Laravel 12 (PHP >= 8.2)
- **Database:** PostgreSQL
- **Frontend:** Vue.js 3 + TypeScript (Composition API)
- **HTTP Client:** Axios (Mandatory for saving data on the frontend)
- **Authentication:** Laravel Sanctum (Personal Access Token / PAT)
- **Testing:** PHPUnit (Backend) & Vitest / Vue Test Utils (Frontend)

# 📜 Core Rules & Constraints

## Backend & Database
1. **User Auth:** NO registration page. The admin user must be created via Seeder. Email input must be unique and properly validated.
2. **Project Management:** Projects CANNOT be deleted (no delete feature). You can only update the status to either `active` or `archive`.
3. **Task Management:** Deleting tasks must use *soft delete* (the `deleted_at` and `deleted_by` fields must be filled when deleted).
4. **Database Requirements:** MUST use Migrations and Seeders. The category field for tasks must fetch data from the `categories` table (via seeder).
5. **Error Handling:** API error alerts/notifications must be clear and descriptive. Display per-field messages if validation fails; generic "Error 500" or generic toast messages are STRICTLY PROHIBITED.
6. **Validation:** All fields are `required` unless explicitly stated as optional.

## Frontend Architecture
The frontend folder structure MUST follow this exact format:
- `public/`: Static files and favicon.
- `components/`: Reusable UI components (Form, Modal, DataTable, Button, Label, File Upload, Loader, Pagination).
- `views/`: Main pages used by the router.
- `routes/`: Routing configuration and navigation guards/middleware.
- `stores/`: State management (Pinia/Vuex).
- `src/services/`: API communication layer (Axios instance, API functions, interceptors).
- `types/`: TypeScript global types/interfaces.
- `plugins/`: Helper functions.
- `main.ts`: Application entry point.
*UI Note:* Responsive design is not mandatory; focus purely on functionality. Boilerplates that include all features out-of-the-box are not allowed.