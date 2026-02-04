<template>
  <q-page class="flex flex-center">
    <div class="text-center">
      <img
        alt="Quasar logo"
        src="~assets/quasar-logo-vertical.svg"
        style="width: 200px; height: 200px"
      />
      <h5 class="q-mt-md q-mb-sm">Welcome to Task Manager</h5>
      <p class="text-grey q-mb-md">A simple CRUD app with Quasar, Vue.js, and PHP</p>
      <q-btn color="primary" label="Go to Tasks" to="/tasks" size="lg" />
    </div>
  </q-page>
</template>

<script setup>
// This is the welcome page
// Task management is available at /tasks route
</script>
