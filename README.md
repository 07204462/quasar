# Mobile App with Quasar (Cordova), Vue.js, and PHP Docker Backend

This project is a full-stack mobile application with:

- **Frontend**: Quasar (Cordova) with Vue.js
- **Backend**: PHP with MySQL (Docker)
- **API**: RESTful CRUD API for task management

## Project Structure

```
.
├── backend/          # PHP backend with Docker
│   ├── Commands/     # Command-line tools
│   ├── Database/     # Database configurations
│   ├── Helpers/      # Helper functions
│   ├── Modules/      # API modules
│   ├── public/       # Public web root
│   ├── System/       # Core system files
│   ├── Test/         # Test files
│   ├── uploads/      # File uploads directory
│   ├── composer.json # PHP dependencies
│   ├── php.Dockerfile # PHP Docker configuration
│   ├── mysql.Dockerfile # MySQL Docker configuration
│   └── docker-compose.yml
├── mobile/           # Quasar mobile app
│   ├── src/          # Vue.js source code
│   ├── public/       # Static assets
│   ├── src-cordova/  # Cordova configuration
│   └── package.json  # Node.js dependencies
└── README.md         # This file
```

## Backend Setup

### Prerequisites

- Docker and Docker Compose
- PHP 8.2+ (optional, for local development)

### Running the Backend

1. Navigate to the backend directory:

   ```bash
   cd backend
   ```

2. Start the Docker containers:

   ```bash
   docker-compose up -d
   ```

3. The backend will be available at:
   - API: http://localhost:8000
   - Database: localhost:3308 (MySQL)
   - phpMyAdmin: http://localhost:8181

### API Endpoints

- `GET /api/health` - Health check
- `POST /api/setup` - Create database table
- `GET /api/tasks` - List all tasks
- `POST /api/tasks` - Create a new task
- `GET /api/tasks/{id}` - Get a specific task
- `PUT /api/tasks/{id}` - Update a task
- `DELETE /api/tasks/{id}` - Delete a task

### Testing the Backend

Run the test script:

```powershell
cd backend
powershell -ExecutionPolicy Bypass -File test_api.ps1
```

### Stop the Backend

```bash
docker-compose down
```

## Mobile App Setup

### Prerequisites

- Node.js 20+
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

3. Start the development server:

   ```bash
   npm run dev
   ```

4. The app will be available at:
   - Web: http://localhost:9000 (or similar port)

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

### Features

The mobile app includes:

- Task management interface (CRUD operations)
- Create, read, update, and delete tasks
- Task status and priority management
- Responsive design with Quasar components
- Clean and responsive UI
- Full CRUD operations with PHP backend
- RESTful API architecture

## Database Schema

The `tasks` table has the following structure:

- `id` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `title` (VARCHAR(255))
- `description` (TEXT)
- `status` (ENUM: 'pending', 'in_progress', 'completed')
- `priority` (ENUM: 'low', 'medium', 'high')
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

## Technology Stack

**Backend:**

- PHP 8.2
- MySQL 8.0
- Docker & Docker Compose
- Composer for dependency management

**Frontend:**

- Quasar Framework
- Vue.js 3
- Axios for API calls
- Cordova for mobile deployment

## Configuration

### Backend Configuration

- Database connection: `backend/System/Config.php`
- Docker settings: `backend/docker-compose.yml`
- PHP configuration: `backend/php.Dockerfile`
- MySQL configuration: `backend/mysql.Dockerfile`

### Mobile App Configuration

- API base URL: `mobile/src/boot/axios.js`
- App layout: `mobile/src/layouts/MainLayout.vue`
- Task management page: `mobile/src/pages/IndexPage.vue`

## Development Notes

### CORS Configuration

The backend includes CORS headers to allow requests from the mobile app.

### Database Port

The MySQL database is exposed on port 3308 to avoid conflicts with other MySQL instances.

### API Response Format

All API responses follow a consistent format:

```json
{
  "success": true,
  "message": "Operation message",
  "data": { ... }
}
```

## Notes

- The backend API runs on `http://localhost:8000`
- For Android emulator, the API URL is configured as `http://10.0.2.2:8000/api`
- For web development, change the baseURL in `mobile/src/boot/axios.js` to `http://localhost:8000/api`

## Troubleshooting

### Backend Issues

1. If ports 8000 or 3308 are in use, update `docker-compose.yml`
2. Check container logs: `docker logs php-api`
3. Test API directly: `curl http://localhost:8000/api/health`

### Mobile App Issues

1. Ensure backend is running before starting the mobile app
2. Check API base URL in `axios.js`
3. Verify npm dependencies are installed

## Next Steps

1. Add user authentication
2. Implement real-time updates
3. Add push notifications
4. Deploy to mobile app stores
5. Add more complex business logic

## License

This project is for educational purposes.
