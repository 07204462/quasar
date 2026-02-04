# Test the API endpoints
Write-Host "Testing API endpoints..." -ForegroundColor Green

# Test health endpoint
Write-Host "`n1. Testing health endpoint..." -ForegroundColor Yellow
try {
    $health = Invoke-RestMethod -Uri "http://localhost:8000/api/health" -Method GET
    Write-Host "   Health check: $($health.status) - $($health.message)" -ForegroundColor Green
} catch {
    Write-Host "   Health check failed: $_" -ForegroundColor Red
}

# Test creating a task
Write-Host "`n2. Testing task creation..." -ForegroundColor Yellow
try {
    $body = @{
        title = "Test Task 1"
        description = "This is the first test task"
        status = "pending"
        priority = "medium"
    } | ConvertTo-Json
    
    $task = Invoke-RestMethod -Uri "http://localhost:8000/api/tasks" -Method POST -Body $body -ContentType "application/json"
    Write-Host "   Task created successfully!" -ForegroundColor Green
    Write-Host "   Task ID: $($task.data.id)" -ForegroundColor Cyan
    Write-Host "   Title: $($task.data.title)" -ForegroundColor Cyan
    
    $taskId = $task.data.id
} catch {
    Write-Host "   Task creation failed: $_" -ForegroundColor Red
    $taskId = 1
}

# Test getting all tasks
Write-Host "`n3. Testing get all tasks..." -ForegroundColor Yellow
try {
    $tasks = Invoke-RestMethod -Uri "http://localhost:8000/api/tasks" -Method GET
    Write-Host "   Found $($tasks.data.Count) tasks" -ForegroundColor Green
    foreach ($t in $tasks.data) {
        Write-Host "   - $($t.id): $($t.title) ($($t.status))" -ForegroundColor Cyan
    }
} catch {
    Write-Host "   Get tasks failed: $_" -ForegroundColor Red
}

# Test getting a single task
Write-Host "`n4. Testing get single task..." -ForegroundColor Yellow
try {
    $task = Invoke-RestMethod -Uri "http://localhost:8000/api/tasks/$taskId" -Method GET
    Write-Host "   Task ${taskId}: $($task.data.title)" -ForegroundColor Green
} catch {
    Write-Host "   Get task failed: $_" -ForegroundColor Red
}

# Test updating a task
Write-Host "`n5. Testing task update..." -ForegroundColor Yellow
try {
    $body = @{
        title = "Updated Test Task"
        status = "in_progress"
    } | ConvertTo-Json
    
    $task = Invoke-RestMethod -Uri "http://localhost:8000/api/tasks/$taskId" -Method PUT -Body $body -ContentType "application/json"
    Write-Host "   Task updated successfully!" -ForegroundColor Green
    Write-Host "   New status: $($task.data.status)" -ForegroundColor Cyan
} catch {
    Write-Host "   Task update failed: $_" -ForegroundColor Red
}

# Test deleting a task
Write-Host "`n6. Testing task deletion..." -ForegroundColor Yellow
try {
    $result = Invoke-RestMethod -Uri "http://localhost:8000/api/tasks/$taskId" -Method DELETE
    Write-Host "   Task deleted successfully!" -ForegroundColor Green
    Write-Host "   Message: $($result.message)" -ForegroundColor Cyan
} catch {
    Write-Host "   Task deletion failed: $_" -ForegroundColor Red
}

Write-Host "`nAPI testing completed!" -ForegroundColor Green