<template>
  <nav class="bg-emerald-600 p-4 shadow-md text-white flex justify-between items-center">
    <div class="text-xl font-bold tracking-wider">📦 Stockz</div>
    <div class="space-x-6 text-sm font-medium">
      <router-link to="/" class="hover:text-emerald-200 transition" active-class="border-b-2 border-white pb-1">Dashboard</router-link>
      <router-link to="/inventory" class="hover:text-emerald-200 transition" active-class="border-b-2 border-white pb-1">Inventaris</router-link>
    </div>
  </nav>
</template>