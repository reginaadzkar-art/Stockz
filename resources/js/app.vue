<template>
  <div class="min-h-screen bg-gray-50 font-sans">
    <Navbar />
    <main class="max-w-6xl mx-auto p-6">
      <!-- Halaman akan berganti-ganti di sini sesuai URL router -->
      <router-view />
    </main>
  </div>
</template>

<script setup>
import Navbar from './Components/Navbar.vue';
</script>