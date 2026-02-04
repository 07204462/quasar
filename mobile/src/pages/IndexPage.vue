<template>
  <q-page class="q-pa-md">
    <div class="text-h4 q-mb-md">Task Manager</div>

    <!-- Create Task Form -->
    <q-card class="q-mb-md">
      <q-card-section>
        <div class="text-h6">Create New Task</div>
        <q-form @submit="createTask" class="q-gutter-md">
          <q-input v-model="newTask.title" label="Title" filled required />
          <q-input v-model="newTask.description" label="Description" filled type="textarea" />
          <q-select v-model="newTask.status" :options="statusOptions" label="Status" filled />
          <q-select v-model="newTask.priority" :options="priorityOptions" label="Priority" filled />
          <div class="row q-gutter-sm">
            <q-btn type="submit" color="primary" label="Create Task" />
            <q-btn @click="resetForm" color="secondary" label="Reset" />
          </div>
        </q-form>
      </q-card-section>
    </q-card>

    <!-- Task List -->
    <q-card>
      <q-card-section>
        <div class="text-h6">Tasks</div>
        <q-table :rows="tasks" :columns="columns" row-key="id" :loading="loading">
          <template v-slot:body-cell-actions="props">
            <q-td :props="props">
              <q-btn
                size="sm"
                color="primary"
                icon="edit"
                @click="editTask(props.row)"
                class="q-mr-xs"
              />
              <q-btn size="sm" color="negative" icon="delete" @click="deleteTask(props.row.id)" />
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- Edit Dialog -->
    <q-dialog v-model="editDialog">
      <q-card style="min-width: 400px">
        <q-card-section>
          <div class="text-h6">Edit Task</div>
        </q-card-section>
        <q-card-section>
          <q-form @submit="updateTask" class="q-gutter-md">
            <q-input v-model="editingTask.title" label="Title" filled required />
            <q-input v-model="editingTask.description" label="Description" filled type="textarea" />
            <q-select v-model="editingTask.status" :options="statusOptions" label="Status" filled />
            <q-select
              v-model="editingTask.priority"
              :options="priorityOptions"
              label="Priority"
              filled
            />
            <div class="row q-gutter-sm justify-end">
              <q-btn type="submit" color="primary" label="Update" />
              <q-btn @click="editDialog = false" color="secondary" label="Cancel" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { api } from 'boot/axios'

const $q = useQuasar()

// Task data
const tasks = ref([])
const loading = ref(false)
const newTask = ref({
  title: '',
  description: '',
  status: 'pending',
  priority: 'medium',
})
const editingTask = ref({})
const editDialog = ref(false)

// Options
const statusOptions = ['pending', 'in_progress', 'completed']
const priorityOptions = ['low', 'medium', 'high']

// Table columns
const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left' },
  { name: 'title', label: 'Title', field: 'title', align: 'left' },
  { name: 'description', label: 'Description', field: 'description', align: 'left' },
  { name: 'status', label: 'Status', field: 'status', align: 'left' },
  { name: 'priority', label: 'Priority', field: 'priority', align: 'left' },
  { name: 'actions', label: 'Actions', align: 'center' },
]

// Fetch tasks
async function fetchTasks() {
  loading.value = true
  try {
    const response = await api.get('/tasks')
    tasks.value = response.data.data
  } catch {
    $q.notify({
      type: 'negative',
      message: 'Failed to fetch tasks',
    })
  } finally {
    loading.value = false
  }
}

// Create task
async function createTask() {
  try {
    await api.post('/tasks', newTask.value)
    $q.notify({
      type: 'positive',
      message: 'Task created successfully',
    })
    resetForm()
    fetchTasks()
  } catch {
    $q.notify({
      type: 'negative',
      message: 'Failed to create task',
    })
  }
}

// Edit task
function editTask(task) {
  editingTask.value = { ...task }
  editDialog.value = true
}

// Update task
async function updateTask() {
  try {
    await api.put(`/tasks/${editingTask.value.id}`, editingTask.value)
    $q.notify({
      type: 'positive',
      message: 'Task updated successfully',
    })
    editDialog.value = false
    fetchTasks()
  } catch {
    $q.notify({
      type: 'negative',
      message: 'Failed to update task',
    })
  }
}

// Delete task
async function deleteTask(id) {
  $q.dialog({
    title: 'Confirm',
    message: 'Are you sure you want to delete this task?',
    cancel: true,
    persistent: true,
  }).onOk(async () => {
    try {
      await api.delete(`/tasks/${id}`)
      $q.notify({
        type: 'positive',
        message: 'Task deleted successfully',
      })
      fetchTasks()
    } catch {
      $q.notify({
        type: 'negative',
        message: 'Failed to delete task',
      })
    }
  })
}

// Reset form
function resetForm() {
  newTask.value = {
    title: '',
    description: '',
    status: 'pending',
    priority: 'medium',
  }
}

// Load tasks on mount
onMounted(() => {
  fetchTasks()
})
</script>
