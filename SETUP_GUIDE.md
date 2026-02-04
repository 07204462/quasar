# Quick Setup Guide

## What's Been Set Up

Your mobile app now has a complete working CRUD system with:

**Backend (PHP + MySQL):**
- RESTful API with full CRUD endpoints
- Docker containerized setup
- MySQL database with tasks table
- Sample data preloaded

**Frontend (Quasar + Vue.js):**
- Task Manager interface
- Create, read, update, and delete tasks
- Mark tasks as complete
- Responsive mobile-first design

## How to Run

### 1. Start the Backend

```bash
cd backend
docker-compose up -d
```

This starts:
- PHP API on http://localhost:8000
- MySQL database on port 3306

Verify the API is running:
```bash
curl http://localhost:8000/api/health
```

Should return: `{"status":"ok","message":"API is running"}`

### 2. Run the Mobile App

```bash
cd mobile
npm install
npm run dev
```

The app will open in your browser automatically at http://localhost:9000

### 3. Test the App

1. Click "Go to Tasks" on the home page
2. You'll see 3 sample tasks already loaded
3. Try adding a new task
4. Mark tasks as complete by clicking the checkbox
5. Delete tasks using the trash icon

## API Endpoints

All endpoints are at `http://localhost:8000/api`

- `GET /health` - Check if API is running
- `GET /tasks` - Get all tasks
- `GET /tasks/:id` - Get one task
- `POST /tasks` - Create a task
- `PUT /tasks/:id` - Update a task
- `DELETE /tasks/:id` - Delete a task

## Building for Android

To test on Android emulator:

1. Make sure the API baseURL in `mobile/src/boot/axios.js` is set to `http://10.0.2.2:8000/api`
2. Add Cordova platform:
   ```bash
   cd mobile
   quasar mode add cordova
   cd src-cordova
   cordova platform add android
   ```
3. Build and run:
   ```bash
   quasar dev -m cordova -T android
   ```

## Stopping the Backend

```bash
cd backend
docker-compose down
```

## Project Structure

```
backend/
├── config/database.php    # Database connection
├── src/Task.php           # Task model
├── public/index.php       # API routes
├── init.sql              # Database schema
└── docker-compose.yml    # Docker setup

mobile/
├── src/
│   ├── pages/
│   │   ├── TasksPage.vue     # Task CRUD interface
│   │   └── IndexPage.vue     # Home page
│   ├── layouts/
│   │   └── MainLayout.vue    # App layout
│   └── boot/
│       └── axios.js          # API configuration
└── quasar.config.js
```

## Troubleshooting

**Backend not responding:**
- Check if Docker containers are running: `docker ps`
- Check logs: `docker-compose logs`

**Mobile app can't connect to API:**
- For web: Use `http://localhost:8000/api`
- For Android emulator: Use `http://10.0.2.2:8000/api`
- Update in `mobile/src/boot/axios.js`

**Database not initializing:**
- Remove volumes: `docker-compose down -v`
- Start fresh: `docker-compose up -d`
