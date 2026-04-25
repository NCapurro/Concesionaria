<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'

const props = defineProps({
  motos: { type: Array, default: () => [] },
  sucursales: { type: Array, default: () => [] },
  motos_catalogo: { type: Array, default: () => [] }, // Recibimos el catálogo
  auth: { type: Object, default: () => ({ user: { name: 'Admin' } }) }
})

const busqueda = ref('')
const filtroEstado = ref('Todos')
const modal = ref(null) 
const motoSeleccionada = ref(null)

// Variables para controlar si mostramos Select o Input
const ingresandoNuevaMarca = ref(false)
const ingresandoNuevoModelo = ref(false)

const form = useForm({
  marca: '', modelo: '', categoria: '', cilindrada: '',
  precio: '', km: 0, anio_fabricacion: new Date().getFullYear(),
  color: '', sucursal_id: '', estado_venta: 'Disponible', dominio: ''
})

// === LOGICA DE MARCAS Y MODELOS ===
const marcasDisponibles = computed(() => {
  const marcas = [...new Set(props.motos_catalogo.map(m => m.marca))]
  return marcas.sort()
})

const modelosDisponibles = computed(() => {
  if (!form.marca || ingresandoNuevaMarca.value) return []
  const modelos = props.motos_catalogo
    .filter(m => m.marca === form.marca)
    .map(m => m.modelo)
  return [...new Set(modelos)].sort()
})

function evaluarMarca() {
  if (form.marca === 'Otro...') {
    form.marca = ''
    ingresandoNuevaMarca.value = true
    form.modelo = ''
    ingresandoNuevoModelo.value = true // Si la marca es nueva, el modelo también debe serlo
  } else {
    form.modelo = '' // Reseteamos el modelo al cambiar de marca
    ingresandoNuevoModelo.value = false
  }
}

function evaluarModelo() {
  if (form.modelo === 'Otro...') {
    form.modelo = ''
    ingresandoNuevoModelo.value = true
  } else {
    // Autocompletar categoría y cilindrada si elige un modelo existente
    const motoFound = props.motos_catalogo.find(m => m.marca === form.marca && m.modelo === form.modelo)
    if (motoFound) {
      form.categoria = motoFound.categoria
      form.cilindrada = motoFound.cilindrada
    }
  }
}
// ==================================

const motosFiltradas = computed(() =>
  props.motos.filter(m => {
    const q = busqueda.value.toLowerCase()
    
    // Extraemos los textos de forma segura
    const marcaStr = m.moto?.marca?.nombre || ''
    const modeloStr = m.moto?.modelo || ''
    const sucursalStr = m.sucursal?.nombre || ''
    const dominioStr = m.dominio || ''

    const matchBusq = !q ||
      marcaStr.toLowerCase().includes(q) ||
      modeloStr.toLowerCase().includes(q) ||
      sucursalStr.toLowerCase().includes(q) ||
      dominioStr.toLowerCase().includes(q)
      
    const matchEstado = filtroEstado.value === 'Todos' || m.estado_venta === filtroEstado.value
    
    return matchBusq && matchEstado
  })
)

const stats = computed(() => ({
  total: props.motos.length,
  disponibles: props.motos.filter(m => m.estado_venta === 'Disponible').length,
  reservadas: props.motos.filter(m => m.estado_venta === 'Reservado').length,
  ceroKm: props.motos.filter(m => m.km === 0).length,
}))

function abrirEditar(moto) {
  motoSeleccionada.value = moto
  
  // Ahora extraemos el ".nombre" de las relaciones
  form.marca = moto.moto?.marca?.nombre || ''
  form.modelo = moto.moto?.modelo || ''
  form.categoria = moto.moto?.categoria?.descripcion || ''
  form.cilindrada = moto.moto?.cilindrada?.descripcion || ''
  
  // Si dejaste color como tabla relacionada, usamos .nombre. Si no, queda directo.
  form.color = moto.color?.nombre || moto.color || '' 
  
  // Estos siguen siendo campos directos de la tabla ejemplars
  form.precio = moto.precio
  form.km = moto.km
  form.anio_fabricacion = moto.anio_fabricacion
  form.sucursal_id = moto.sucursal_id
  form.estado_venta = moto.estado_venta
  form.dominio = moto.dominio ?? ''
  
  // Reseteamos los inputs manuales
  ingresandoNuevaMarca.value = false
  ingresandoNuevoModelo.value = false
  modal.value = 'editar'
}

function abrirEliminar(moto) {
  motoSeleccionada.value = moto
  modal.value = 'eliminar'
}

function abrirCrear() {
  form.reset()
  ingresandoNuevaMarca.value = false
  ingresandoNuevoModelo.value = false
  motoSeleccionada.value = null
  modal.value = 'crear'
}

function cerrarModal() {
  modal.value = null
  motoSeleccionada.value = null
}

function guardar() {
  if (modal.value === 'crear') {
    form.post(route('admin.motos.store'), { preserveScroll: true, onSuccess: () => cerrarModal() })
  } else {
    form.put(route('admin.motos.update', motoSeleccionada.value.id), { preserveScroll: true, onSuccess: () => cerrarModal() })
  }
}

function eliminar() {
  router.delete(route('admin.motos.destroy', motoSeleccionada.value.id), { preserveScroll: true, onSuccess: () => cerrarModal() })
}

const estadoBadge = {
  'Disponible': 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30',
  'Reservado': 'bg-amber-500/20 text-amber-400 border border-amber-500/30',
  'Vendido': 'bg-red-500/20 text-red-400 border border-red-500/30',
}

const fmt = (n) => new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS', maximumFractionDigits: 0 }).format(Number(n))
</script>

<template>
  <Head title="Admin — Stock de Motos" />

  <div class="min-h-screen bg-zinc-950 text-white" style="font-family: 'Montserrat', sans-serif;">

    <!-- Sidebar + Content layout -->
    <div class="flex">

      <!-- Sidebar -->
      <aside class="w-64 min-h-screen bg-zinc-900 border-r border-zinc-800 hidden md:flex flex-col">
        <div class="p-6 border-b border-zinc-800">
          <Link :href="route('home')" class="flex items-center gap-3">
            <div class="w-8 h-8 bg-orange-500 rounded-xl flex items-center justify-center">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
            </div>
            <span class="font-black text-white text-sm">MotoShop<span class="text-orange-500">.</span></span>
          </Link>
        </div>

        <nav class="p-4 flex-1 space-y-1">
          <p class="text-xs text-zinc-600 uppercase tracking-widest px-3 mb-3">Panel</p>
          <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-orange-500/10 text-orange-400 text-sm font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Stock de Motos
          </a>
          <Link :href="route('home')" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-zinc-500 hover:text-zinc-300 hover:bg-zinc-800 text-sm font-medium transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Ver sitio
          </Link>
        </nav>

        <div class="p-4 border-t border-zinc-800">
          <div class="flex items-center gap-3 px-3 py-2">
            <div class="w-8 h-8 bg-orange-500/20 rounded-full flex items-center justify-center text-orange-400 text-xs font-bold">
              {{ auth.user.name.charAt(0) }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold truncate">{{ auth.user.name }}</p>
              <p class="text-xs text-zinc-600">Administrador</p>
            </div>
          </div>
          <Link :href="route('logout')" method="post" as="button"
            class="w-full mt-2 text-xs text-zinc-600 hover:text-zinc-400 text-left px-3 py-1 transition-colors">
            Cerrar sesión
          </Link>
        </div>
      </aside>

      <!-- Main content -->
      <main class="flex-1 overflow-auto">

        <!-- Top bar -->
        <div class="bg-zinc-950 border-b border-zinc-800 px-8 py-5 flex items-center justify-between sticky top-0 z-10">
          <div>
            <h1 class="text-xl font-black tracking-tight">Gestión de Stock</h1>
            <p class="text-xs text-zinc-600 mt-0.5">{{ motos.length }} unidades registradas</p>
          </div>
          <button @click="abrirCrear"
            class="flex items-center gap-2 bg-orange-500 hover:bg-orange-400 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition-all active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Nueva moto
          </button>
        </div>

        <div class="p-8">

          <!-- Stats -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div v-for="stat in [
              { label: 'Total', value: stats.total, color: 'text-white' },
              { label: 'Disponibles', value: stats.disponibles, color: 'text-emerald-400' },
              { label: 'Reservadas', value: stats.reservadas, color: 'text-amber-400' },
              { label: '0 km', value: stats.ceroKm, color: 'text-orange-400' },
            ]" :key="stat.label"
              class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5">
              <p class="text-xs text-zinc-600 uppercase tracking-widest mb-2">{{ stat.label }}</p>
              <p :class="['text-3xl font-black', stat.color]">{{ stat.value }}</p>
            </div>
          </div>

          <!-- Filtros barra -->
          <div class="flex flex-wrap gap-3 mb-6">
            <div class="flex-1 min-w-64 relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              <input v-model="busqueda" type="text" placeholder="Buscar por marca, modelo, sucursal..."
                class="w-full bg-zinc-900 border border-zinc-800 text-white text-sm rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:border-orange-500 placeholder-zinc-600" />
            </div>
            <select v-model="filtroEstado"
              class="bg-zinc-900 border border-zinc-800 text-zinc-400 text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-orange-500">
              <option value="Todos">Todos los estados</option>
              <option value="Disponible">Disponible</option>
              <option value="Reservado">Reservado</option>
              <option value="Vendido">Vendido</option>
            </select>
          </div>

          <!-- Tabla -->
          <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full text-sm">
                <thead>
                  <tr class="border-b border-zinc-800 text-left">
                    <th class="px-5 py-3.5 text-xs text-zinc-600 uppercase tracking-widest font-semibold">ID</th>
                    <th class="px-5 py-3.5 text-xs text-zinc-600 uppercase tracking-widest font-semibold">Moto</th>
                    <th class="px-5 py-3.5 text-xs text-zinc-600 uppercase tracking-widest font-semibold">Precio</th>
                    <th class="px-5 py-3.5 text-xs text-zinc-600 uppercase tracking-widest font-semibold">Km</th>
                    <th class="px-5 py-3.5 text-xs text-zinc-600 uppercase tracking-widest font-semibold">Año</th>
                    <th class="px-5 py-3.5 text-xs text-zinc-600 uppercase tracking-widest font-semibold">Sucursal</th>
                    <th class="px-5 py-3.5 text-xs text-zinc-600 uppercase tracking-widest font-semibold">Estado</th>
                    <th class="px-5 py-3.5 text-xs text-zinc-600 uppercase tracking-widest font-semibold"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="moto in motosFiltradas" :key="moto.id"
                    class="border-b border-zinc-800/60 last:border-0 hover:bg-zinc-800/30 transition-colors">
                    <td class="px-5 py-4 text-zinc-600 font-mono text-xs">#{{ moto.id }}</td>
                    <td class="px-5 py-4">
                      <p class="font-bold text-white">{{ moto.moto?.marca?.descripcion }} {{ moto.moto?.modelo }}</p>
                      <p class="text-xs text-zinc-500 mt-0.5">{{ moto.moto?.cilindrada?.descripcion }} · {{ moto.color?.nombre || moto.color }}</p>
                    </td>
                    <td class="px-5 py-4 font-bold text-orange-400">{{ fmt(moto.precio) }}</td>
                    <td class="px-5 py-4 text-zinc-400">{{ moto.km === 0 ? '0 km' : `${moto.km.toLocaleString('es-AR')} km` }}</td>
                    <td class="px-5 py-4 text-zinc-400">{{ moto.anio_fabricacion }}</td>
                    <td class="px-5 py-4 text-zinc-400">{{ moto.sucursal ? moto.sucursal.nombre : 'Sin sucursal' }}</td>
                    <td class="px-5 py-4">
                      <span :class="['px-2.5 py-1 rounded-full text-xs font-bold', estadoBadge[moto.estado_venta]]">
                        {{ moto.estado_venta }}
                      </span>
                    </td>
                    <td class="px-5 py-4">
                      <div class="flex items-center gap-2 justify-end">
                        <button @click="abrirEditar(moto)"
                          class="p-2 text-zinc-500 hover:text-white hover:bg-zinc-700 rounded-lg transition-all">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button @click="abrirEliminar(moto)"
                          class="p-2 text-zinc-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-all">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-if="motosFiltradas.length === 0" class="text-center py-16 text-zinc-600">
              <p class="text-4xl mb-3">—</p>
              <p>Sin resultados para los filtros aplicados.</p>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- ░░ MODAL CREAR / EDITAR ░░ -->
    <div v-if="modal === 'crear' || modal === 'editar'"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
      style="background: rgba(0,0,0,0.75); backdrop-filter: blur(4px);">
      <div class="bg-zinc-900 border border-zinc-700 rounded-3xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-7 border-b border-zinc-800 flex items-center justify-between">
          <h2 class="text-xl font-black">{{ modal === 'crear' ? 'Nueva moto' : 'Editar moto' }}</h2>
          <button @click="cerrarModal" class="text-zinc-600 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <div class="p-7 space-y-5">
          <div class="grid grid-cols-2 gap-4">
            <div>
  <label class="text-xs text-zinc-500 uppercase tracking-widest mb-2 flex items-center justify-between">
    <span>Marca</span>
    <button v-if="ingresandoNuevaMarca" @click="ingresandoNuevaMarca = false; form.marca = ''" type="button" class="text-orange-500 hover:text-orange-400 font-bold">Volver a lista</button>
  </label>
  
  <select v-if="!ingresandoNuevaMarca" v-model="form.marca" @change="evaluarMarca" class="w-full bg-zinc-800 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-orange-500">
    <option value="" disabled>Seleccione marca...</option>
    <option v-for="marca in marcasDisponibles" :key="marca" :value="marca">{{ marca }}</option>
    <option value="Otro...">+ Cargar otra marca...</option>
  </select>

  <input v-else v-model="form.marca" type="text" placeholder="Ej. Zanella" class="w-full bg-zinc-800 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-orange-500 placeholder-zinc-600" />
</div>

<div>
  <label class="text-xs text-zinc-500 uppercase tracking-widest mb-2 flex items-center justify-between">
    <span>Modelo</span>
    <button v-if="ingresandoNuevoModelo && !ingresandoNuevaMarca" @click="ingresandoNuevoModelo = false; form.modelo = ''" type="button" class="text-orange-500 hover:text-orange-400 font-bold">Volver a lista</button>
  </label>

  <select v-if="!ingresandoNuevoModelo" v-model="form.modelo" @change="evaluarModelo" :disabled="!form.marca" class="w-full bg-zinc-800 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-orange-500 disabled:opacity-50">
    <option value="" disabled>Seleccione modelo...</option>
    <option v-for="modelo in modelosDisponibles" :key="modelo" :value="modelo">{{ modelo }}</option>
    <option value="Otro...">+ Cargar otro modelo...</option>
  </select>

  <input v-else v-model="form.modelo" type="text" placeholder="Ej. ZB 110" class="w-full bg-zinc-800 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-orange-500 placeholder-zinc-600" />
</div>
            <div>
              <label class="text-xs text-zinc-500 uppercase tracking-widest mb-2 block">Categoría</label>
              <select v-model="form.categoria"
                class="w-full bg-zinc-800 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-orange-500">
                <option value="">Seleccionar...</option>
                <option>Enduro</option><option>Street</option><option>Naked</option>
                <option>Pista</option><option>Trail</option><option>Custom</option>
              </select>
            </div>
            <div>
              <label class="text-xs text-zinc-500 uppercase tracking-widest mb-2 block">Cilindrada</label>
              <input v-model="form.cilindrada" type="text" placeholder="ej. 250cc"
                class="w-full bg-zinc-800 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-orange-500 placeholder-zinc-600" />
            </div>
            <div>
              <label class="text-xs text-zinc-500 uppercase tracking-widest mb-2 block">Precio ($)</label>
              <input v-model="form.precio" type="number" placeholder="ej. 6500000"
                class="w-full bg-zinc-800 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-orange-500 placeholder-zinc-600" />
            </div>
            <div>
              <label class="text-xs text-zinc-500 uppercase tracking-widest mb-2 block">Kilometraje</label>
              <input v-model="form.km" type="number" placeholder="0"
                class="w-full bg-zinc-800 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-orange-500 placeholder-zinc-600" />
            </div>
            <div>
              <label class="text-xs text-zinc-500 uppercase tracking-widest mb-2 block">Año de fabricación</label>
              <input v-model="form.anio_fabricacion" type="number" placeholder="2024"
                class="w-full bg-zinc-800 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-orange-500 placeholder-zinc-600" />
            </div>
            <div>
              <label class="text-xs text-zinc-500 uppercase tracking-widest mb-2 block">Color</label>
              <input v-model="form.color" type="text" placeholder="ej. Rojo"
                class="w-full bg-zinc-800 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-orange-500 placeholder-zinc-600" />
            </div>
            <div>
              <label class="text-xs text-zinc-500 uppercase tracking-widest mb-2 block">Sucursal</label>
              <select v-model="form.sucursal_id"
                class="w-full bg-zinc-800 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-orange-500">
                <option value="" disabled>Seleccione una sucursal...</option>
    <option v-for="sucursal in sucursales" :key="sucursal.id" :value="sucursal.id">
      {{ sucursal.nombre }}
    </option>
              </select>
            </div>
            <div>
              <label class="text-xs text-zinc-500 uppercase tracking-widest mb-2 block">Estado</label>
              <select v-model="form.estado_venta"
                class="w-full bg-zinc-800 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-orange-500">
                <option>Disponible</option>
                <option>Reservado</option>
                <option>Vendido</option>
              </select>
            </div>
            <div class="col-span-2">
              <label class="text-xs text-zinc-500 uppercase tracking-widest mb-2 block">Dominio (opcional)</label>
              <input v-model="form.dominio" type="text" placeholder="ej. A123BCD"
                class="w-full bg-zinc-800 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-orange-500 placeholder-zinc-600 uppercase" />
            </div>
          </div>
        </div>

        <div class="p-7 border-t border-zinc-800 flex gap-3 justify-end">
          <button @click="cerrarModal"
            class="px-6 py-2.5 border border-zinc-700 hover:border-zinc-500 text-zinc-400 hover:text-white rounded-xl text-sm font-semibold transition-all">
            Cancelar
          </button>
          <button @click="guardar" :disabled="form.processing"
            class="px-6 py-2.5 bg-orange-500 hover:bg-orange-400 text-white font-bold rounded-xl text-sm transition-all active:scale-95 disabled:opacity-50">
            {{ form.processing ? 'Guardando...' : (modal === 'crear' ? 'Crear moto' : 'Guardar cambios') }}
          </button>
        </div>
      </div>
    </div>

    <!-- ░░ MODAL ELIMINAR ░░ -->
    <div v-if="modal === 'eliminar'"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
      style="background: rgba(0,0,0,0.75); backdrop-filter: blur(4px);">
      <div class="bg-zinc-900 border border-zinc-700 rounded-3xl w-full max-w-md p-8 text-center">
        <div class="w-14 h-14 bg-red-500/10 border border-red-500/20 rounded-2xl flex items-center justify-center mx-auto mb-5">
          <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <h2 class="text-xl font-black mb-2">¿Eliminar esta moto?</h2>
        <p class="text-zinc-500 text-sm mb-1">
          <strong class="text-white">{{ motoSeleccionada?.moto?.marca?.nombre }} {{ motoSeleccionada?.moto?.modelo }}</strong>
        </p>
        <p class="text-zinc-600 text-sm mb-8">Esta acción no se puede deshacer.</p>
        <div class="flex gap-3">
          <button @click="cerrarModal"
            class="flex-1 border border-zinc-700 hover:border-zinc-500 text-zinc-400 hover:text-white rounded-xl py-3 text-sm font-semibold transition-all">
            Cancelar
          </button>
          <button @click="eliminar"
            class="flex-1 bg-red-600 hover:bg-red-500 text-white font-bold rounded-xl py-3 text-sm transition-all">
            Sí, eliminar
          </button>
        </div>
      </div>
    </div>

  </div>
</template>