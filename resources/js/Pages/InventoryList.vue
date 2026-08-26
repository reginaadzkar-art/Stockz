<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-2xl font-bold text-gray-800">Daftar Inventaris</h2>
        <p class="text-gray-600 text-sm mt-1">Daftar produk, stok barang, dan varian inventaris gudang.</p>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm text-gray-600">
        <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
          <tr>
            <th class="py-3 px-4">Nama Barang</th>
            <th class="py-3 px-4">Kategori</th>
            <th class="py-3 px-4">Total Stok</th>
            <th class="py-3 px-4">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr class="hover:bg-gray-50 transition">
            <td colspan="4" class="py-6 text-center text-gray-400">
              Data inventaris siap dimuat dari API atau database.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
</script>
