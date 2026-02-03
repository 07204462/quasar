# Task Manager App

A mobile application built with Quasar (Cordova), Vue.js, and PHP backend with MySQL database.

## Project Structure

```
.
├── backend/          # PHP API backend
│   ├── config/       # Database configuration
│   ├── docker/       # Docker configuration
│   ├── public/       # Public files (entry point)
│   ├── src/          # Source code (models)
│   └── init.sql      # Database initialization
└── mobile/           # Quasar mobile app
    └── src/          # Vue.js source files
```

## Backend Setup

### Prerequisites
- Docker & Docker Compose

### Running the Backend

1. Navigate to the backend directory:
```bash
cd backend
```

2. Start the containers:
```bash
docker-compose up -d
```

This will:
- Start a PHP 8.2 container on port 8000
- Start a MySQL 8.0 container on port 3306
- Automatically create the `tasks` table with sample data

3. Test the API:
```bash
curl http://localhost:8000/api/health
```

### API Endpoints

- `GET /api/health` - Health check
- `GET /api/tasks` - Get all tasks
- `GET /api/tasks/:id` - Get a specific task
- `POST /api/tasks` - Create a new task
- `PUT /api/tasks/:id` - Update a task
- `DELETE /api/tasks/:id` - Delete a task

### Stop the Backend

```bash
docker-compose down
```

## Mobile App Setup

### Prerequisites
- Node.js (v20, v22, v24, v26, or v28)
- npm or yarn

### Running the Mobile App

1. Navigate to the mobile directory:
```bash
cd mobile
```

2. Install dependencies:
```bash
npm install
```

3. Run in development mode:
```bash
npm run dev
```

The app will open in your browser automatically.

### Building for Android

1. Add Cordova Android platform:
```bash
quasar mode add cordova
cd src-cordova
cordova platform add android
```

2. Build the app:
```bash
quasar build -m cordova -T android
```

## Features

- Create, read, update, and delete tasks
- Mark tasks as completed
- Clean and responsive UI
- Full CRUD operations with PHP backend
- RESTful API architecture

## Technology Stack

**Backend:**
- PHP 8.2
- MySQL 8.0
- Docker & Docker Compose

**Frontend:**
- Quasar Framework
- Vue.js 3
- Axios for API calls
- Cordova for mobile deployment

## Notes

- The backend API runs on `http://localhost:8000`
- For Android emulator, the API URL is configured as `http://10.0.2.2:8000/api`
- For web development, change the baseURL in `mobile/src/boot/axios.js` to `http://localhost:8000/api`
