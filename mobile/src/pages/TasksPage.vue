<template>
  <q-page padding>
    <div class="q-pa-md">
      <h4 class="q-mt-none q-mb-md">Task Manager</h4>

      <q-card class="q-mb-md">
        <q-card-section>
          <div class="text-h6">Add New Task</div>
        </q-card-section>

        <q-card-section>
          <q-input
            v-model="newTask.title"
            label="Task Title"
            outlined
            dense
            class="q-mb-sm"
          />
          <q-input
            v-model="newTask.description"
            label="Description"
            outlined
            dense
            type="textarea"
            rows="2"
            class="q-mb-sm"
          />
          <q-btn
            color="primary"
            label="Add Task"
            @click="addTask"
            :loading="loading"
          />
        </q-card-section>
      </q-card>

      <div v-if="tasks.length === 0" class="text-center q-pa-md text-grey">
        No tasks yet. Add one above!
      </div>

      <q-card
        v-for="task in tasks"
        :key="task.id"
        class="q-mb-md"
      >
        <q-card-section>
          <div class="row items-center">
            <q-checkbox
              v-model="task.completed"
              :true-value="1"
              :false-value="0"
              @update:model-value="updateTask(task)"
            />
            <div class="col">
              <div :class="task.completed ? 'text-strike' : ''" class="text-weight-medium">
                {{ task.title }}
              </div>
              <div class="text-caption text-grey">{{ task.description }}</div>
            </div>
            <q-btn
              flat
              round
              dense
              icon="delete"
              color="negative"
              @click="deleteTask(task.id)"
            />
          </div>
        </q-card-section>
      </q-card>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { api } from 'boot/axios'
import { useQuasar } from 'quasar'

const $q = useQuasar()
const tasks = ref([])
const loading = ref(false)
const newTask = ref({
  title: '',
  description: ''
})

async function fetchTasks() {
  try {
    const response = await api.get('/tasks')
    tasks.value = response.data
  } catch {
    $q.notify({
      type: 'negative',
      message: 'Failed to load tasks'
    })
  }
}

async function addTask() {
  if (!newTask.value.title) {
    $q.notify({
      type: 'warning',
      message: 'Please enter a task title'
    })
    return
  }

  loading.value = true
  try {
    await api.post('/tasks', {
      title: newTask.value.title,
      description: newTask.value.description,
      completed: false
    })

    newTask.value = { title: '', description: '' }
    await fetchTasks()

    $q.notify({
      type: 'positive',
      message: 'Task added successfully'
    })
  } catch {
    $q.notify({
      type: 'negative',
      message: 'Failed to add task'
    })
  } finally {
    loading.value = false
  }
}

async function updateTask(task) {
  try {
    await api.put(`/tasks/${task.id}`, {
      title: task.title,
      description: task.description,
      completed: task.completed
    })

    $q.notify({
      type: 'positive',
      message: 'Task updated'
    })
  } catch {
    $q.notify({
      type: 'negative',
      message: 'Failed to update task'
    })
    await fetchTasks()
  }
}

async function deleteTask(id) {
  try {
    await api.delete(`/tasks/${id}`)
    await fetchTasks()

    $q.notify({
      type: 'positive',
      message: 'Task deleted'
    })
  } catch {
    $q.notify({
      type: 'negative',
      message: 'Failed to delete task'
    })
  }
}

onMounted(() => {
  fetchTasks()
})
</script>
