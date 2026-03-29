<script setup>
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
 
const props = defineProps({
  motos: {
    type: Array,
    default: () => [
      {
        id_ejemplar: 101,
        precio: 6500000,
        km: 0,
        anio_fabricacion: 2024,
        dominio: null,
        estado_venta: "Disponible",
        color: "Rojo",
        sucursal: "Venado Tuerto",
        moto: { marca: "Honda", modelo: "Tornado", categoria: "Enduro", cilindrada: "250cc" },
        imagenes: [
          "https://images.unsplash.com/photo-1568772585407-9361f9bf3c87?q=80&w=800&auto=format&fit=crop",
          "https://images.unsplash.com/photo-1597157639073-69284dc0f6ea?q=80&w=800&auto=format&fit=crop"
        ]
      },
      {
        id_ejemplar: 102,
        precio: 2100000,
        km: 14500,
        anio_fabricacion: 2021,
        dominio: "A123BCD",
        estado_venta: "Disponible",
        color: "Negro",
        sucursal: "Paraná",
        moto: { marca: "Yamaha", modelo: "YBR", categoria: "Street", cilindrada: "125cc" },
        imagenes: [
          "https://images.unsplash.com/photo-1558981403-c5f9899a28bc?q=80&w=800&auto=format&fit=crop"
        ]
      },
      {
        id_ejemplar: 103,
        precio: 11500000,
        km: 0,
        anio_fabricacion: 2024,
        dominio: null,
        estado_venta: "Reservado",
        color: "Verde",
        sucursal: "Paraná",
        moto: { marca: "Kawasaki", modelo: "Ninja 400", categoria: "Pista", cilindrada: "400cc" },
        imagenes: [
          "https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?q=80&w=800&auto=format&fit=crop"
        ]
      }
    ]
  }
})
 
const filtroCategoria = ref('Todos')
const filtroSucursal = ref('Todas')
const filtroEstado = ref('Todos')
const imagenActiva = ref({})
 
const categorias = computed(() => {
  const cats = [...new Set(props.motos.map(m => m.moto.categoria))]
  return ['Todos', ...cats]
})
 
const sucursales = computed(() => {
  const suc = [...new Set(props.motos.map(m => m.sucursal))]
  return ['Todas', ...suc]
})
 
const motosFiltradas = computed(() => {
  return props.motos.filter(m => {
    const cat = filtroCategoria.value === 'Todos' || m.moto.categoria === filtroCategoria.value
    const suc = filtroSucursal.value === 'Todas' || m.sucursal === filtroSucursal.value
    const est = filtroEstado.value === 'Todos' || m.estado_venta === filtroEstado.value
    return cat && suc && est
  })
})
 
function getImagenActiva(id, imagenes) {
  return imagenes[imagenActiva.value[id] ?? 0]
}
 
function setImagen(id, idx) {
  imagenActiva.value[id] = idx
}
 
function formatPrecio(precio) {
  return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS', maximumFractionDigits: 0 }).format(precio)
}
 
function formatKm(km) {
  return km === 0 ? '0 km' : new Intl.NumberFormat('es-AR').format(km) + ' km'
}
 
const estadoClases = {
  'Disponible': 'bg-emerald-100 text-emerald-800 border border-emerald-200',
  'Reservado': 'bg-amber-100 text-amber-800 border border-amber-200',
  'Vendido': 'bg-red-100 text-red-800 border border-red-200',
}
</script>
 
<template>
  <Head title="Catálogo de Motos" />
 
  <div class="min-h-screen bg-zinc-950 text-white font-sans">
 
    <!-- HERO HEADER -->
    <header class="relative overflow-hidden bg-zinc-950 border-b border-zinc-800">
      <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 w-64 h-64 bg-orange-600/8 rounded-full blur-2xl"></div>
      </div>
      <div class="relative max-w-7xl mx-auto px-6 py-8 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <div class="w-10 h-10 rounded-xl bg-orange-500 flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>
          <div>
            <h1 class="text-xl font-bold tracking-tight leading-none">MotoShop</h1>
            <p class="text-xs text-zinc-500 mt-0.5 tracking-widest uppercase">Concesionaria oficial</p>
          </div>
        </div>
        <nav class="hidden md:flex items-center gap-8 text-sm text-zinc-400">
          <a href="#" class="hover:text-white transition-colors">Inicio</a>
          <a href="#" class="text-orange-400 font-medium">Catálogo</a>
          <a href="#" class="hover:text-white transition-colors">Servicios</a>
          <a href="#" class="hover:text-white transition-colors">Contacto</a>
        </nav>
      </div>
    </header>
 
    <!-- SECTION TITLE + STATS -->
    <section class="max-w-7xl mx-auto px-6 pt-14 pb-10">
      <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div>
          <p class="text-orange-500 text-xs font-semibold tracking-widest uppercase mb-2">Nuestro stock</p>
          <h2 class="text-4xl md:text-5xl font-extrabold tracking-tight leading-none">
            Catálogo<br>
            <span class="text-zinc-400">de motos</span>
          </h2>
        </div>
        <div class="flex gap-6">
          <div class="text-center">
            <p class="text-3xl font-bold text-orange-400">{{ props.motos.filter(m => m.km === 0).length }}</p>
            <p class="text-xs text-zinc-500 uppercase tracking-widest mt-1">0 km</p>
          </div>
          <div class="w-px bg-zinc-800"></div>
          <div class="text-center">
            <p class="text-3xl font-bold text-orange-400">{{ props.motos.filter(m => m.km > 0).length }}</p>
            <p class="text-xs text-zinc-500 uppercase tracking-widest mt-1">Usadas</p>
          </div>
          <div class="w-px bg-zinc-800"></div>
          <div class="text-center">
            <p class="text-3xl font-bold text-orange-400">{{ props.motos.length }}</p>
            <p class="text-xs text-zinc-500 uppercase tracking-widest mt-1">Total</p>
          </div>
        </div>
      </div>
    </section>
 
    <!-- FILTROS -->
    <section class="max-w-7xl mx-auto px-6 pb-10">
      <div class="flex flex-wrap gap-3 items-center">
 
        <!-- Categoría -->
        <div class="flex items-center gap-1 bg-zinc-900 border border-zinc-800 rounded-xl px-1 py-1">
          <button
            v-for="cat in categorias"
            :key="cat"
            @click="filtroCategoria = cat"
            :class="[
              'px-4 py-1.5 rounded-lg text-sm font-medium transition-all duration-200',
              filtroCategoria === cat
                ? 'bg-orange-500 text-white shadow-sm'
                : 'text-zinc-400 hover:text-white'
            ]"
          >{{ cat }}</button>
        </div>
 
        <!-- Sucursal -->
        <select
          v-model="filtroSucursal"
          class="bg-zinc-900 border border-zinc-800 text-zinc-300 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-orange-500 cursor-pointer"
        >
          <option v-for="s in sucursales" :key="s" :value="s">{{ s }}</option>
        </select>
 
        <!-- Estado -->
        <select
          v-model="filtroEstado"
          class="bg-zinc-900 border border-zinc-800 text-zinc-300 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-orange-500 cursor-pointer"
        >
          <option value="Todos">Todos los estados</option>
          <option value="Disponible">Disponible</option>
          <option value="Reservado">Reservado</option>
          <option value="Vendido">Vendido</option>
        </select>
 
        <span class="text-zinc-600 text-sm ml-auto">
          {{ motosFiltradas.length }} resultado{{ motosFiltradas.length !== 1 ? 's' : '' }}
        </span>
      </div>
    </section>
 
    <!-- GRID DE CARDS -->
    <main class="max-w-7xl mx-auto px-6 pb-20">
      <div
        v-if="motosFiltradas.length === 0"
        class="text-center py-24 text-zinc-600"
      >
        <p class="text-4xl mb-4">—</p>
        <p class="text-lg">Sin resultados para los filtros seleccionados.</p>
      </div>
 
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <article
          v-for="moto in motosFiltradas"
          :key="moto.id_ejemplar"
          class="group bg-zinc-900 rounded-2xl border border-zinc-800 overflow-hidden hover:border-zinc-600 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-black/40"
        >
          <!-- IMAGEN -->
          <div class="relative aspect-[4/3] bg-zinc-800 overflow-hidden">
            <img
              :src="getImagenActiva(moto.id_ejemplar, moto.imagenes)"
              :alt="`${moto.moto.marca} ${moto.moto.modelo}`"
              class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
            />
 
            <!-- Overlay oscuro si Reservado/Vendido -->
            <div
              v-if="moto.estado_venta !== 'Disponible'"
              class="absolute inset-0 bg-zinc-950/50 backdrop-blur-[1px]"
            ></div>
 
            <!-- Badge estado -->
            <span
              :class="['absolute top-3 left-3 px-2.5 py-1 rounded-full text-xs font-semibold', estadoClases[moto.estado_venta]]"
            >{{ moto.estado_venta }}</span>
 
            <!-- Badge 0km / Usado -->
            <span class="absolute top-3 right-3 px-2.5 py-1 rounded-full text-xs font-semibold"
              :class="moto.km === 0
                ? 'bg-orange-500 text-white'
                : 'bg-zinc-700 text-zinc-300'"
            >{{ moto.km === 0 ? '0 km' : 'Usada' }}</span>
 
            <!-- Dots de imágenes -->
            <div
              v-if="moto.imagenes.length > 1"
              class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5"
            >
              <button
                v-for="(img, idx) in moto.imagenes"
                :key="idx"
                @click="setImagen(moto.id_ejemplar, idx)"
                :class="[
                  'w-1.5 h-1.5 rounded-full transition-all',
                  (imagenActiva[moto.id_ejemplar] ?? 0) === idx
                    ? 'bg-white scale-125'
                    : 'bg-white/40 hover:bg-white/70'
                ]"
              />
            </div>
          </div>
 
          <!-- CONTENIDO -->
          <div class="p-5">
            <!-- Marca + Modelo -->
            <div class="flex items-start justify-between mb-3">
              <div>
                <p class="text-xs font-semibold text-orange-400 uppercase tracking-widest">{{ moto.moto.marca }}</p>
                <h3 class="text-xl font-bold tracking-tight leading-tight mt-0.5">{{ moto.moto.modelo }}</h3>
              </div>
              <span class="text-xs bg-zinc-800 text-zinc-400 px-2 py-1 rounded-lg border border-zinc-700">
                {{ moto.moto.cilindrada }}
              </span>
            </div>
 
            <!-- Tags -->
            <div class="flex flex-wrap gap-2 mb-4">
              <span class="text-xs text-zinc-400 bg-zinc-800 px-2.5 py-1 rounded-lg">
                {{ moto.moto.categoria }}
              </span>
              <span class="text-xs text-zinc-400 bg-zinc-800 px-2.5 py-1 rounded-lg">
                {{ moto.color }}
              </span>
              <span class="text-xs text-zinc-400 bg-zinc-800 px-2.5 py-1 rounded-lg">
                {{ moto.anio_fabricacion }}
              </span>
            </div>
 
            <!-- Info secundaria -->
            <div class="flex items-center justify-between text-xs text-zinc-500 mb-5 pb-5 border-b border-zinc-800">
              <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ moto.sucursal }}
              </span>
              <span v-if="moto.km > 0" class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ formatKm(moto.km) }}
              </span>
              <span v-if="moto.dominio" class="font-mono tracking-wider">{{ moto.dominio }}</span>
            </div>
 
            <!-- Precio + CTA -->
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs text-zinc-500 mb-0.5">Precio</p>
                <p class="text-2xl font-extrabold text-white leading-none tracking-tight">
                  {{ formatPrecio(moto.precio) }}
                </p>
              </div>
              <button
                :disabled="moto.estado_venta !== 'Disponible'"
                :class="[
                  'px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200',
                  moto.estado_venta === 'Disponible'
                    ? 'bg-orange-500 hover:bg-orange-400 text-white hover:shadow-lg hover:shadow-orange-500/25 active:scale-95'
                    : 'bg-zinc-800 text-zinc-600 cursor-not-allowed'
                ]"
              >
                {{ moto.estado_venta === 'Disponible' ? 'Consultar' : moto.estado_venta }}
              </button>
            </div>
          </div>
        </article>
      </div>
    </main>
 
    <!-- FOOTER MÍNIMO -->
    <footer class="border-t border-zinc-800 py-8">
      <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-4 text-zinc-600 text-sm">
        <p>© {{ new Date().getFullYear() }} MotoShop · Todos los derechos reservados.</p>
        <div class="flex gap-6">
          <a href="#" class="hover:text-zinc-400 transition-colors">Términos</a>
          <a href="#" class="hover:text-zinc-400 transition-colors">Privacidad</a>
          <a href="#" class="hover:text-zinc-400 transition-colors">Contacto</a>
        </div>
      </div>
    </footer>
  </div>
</template>
 