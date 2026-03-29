<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
  moto: { type: Object, required: true },
  todasLasMotos: { type: Array, default: () => [] }
});

const emit = defineEmits(['close', 'cambiarMoto']);

const imgActiva = ref(0);

// Reseteamos la imagen a 0 cada vez que cambia la moto (ej: al clickear una relacionada)
watch(() => props.moto, () => { imgActiva.value = 0; });

const fmt = (n) => new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS', maximumFractionDigits: 0 }).format(n);

const waMsg = computed(() =>
  `Hola! Me interesa la ${props.moto.moto.marca} ${props.moto.moto.modelo} ${props.moto.anio_fabricacion} (ID #${props.moto.id_ejemplar}). ¿Está disponible?`
);

const estadoColor = {
  'Disponible': 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30',
  'Reservado': 'bg-amber-500/20 text-amber-400 border border-amber-500/30',
  'Vendido': 'bg-red-500/20 text-red-400 border border-red-500/30',
};

// Filtramos las relacionadas dinámicamente
const relacionadas = computed(() => {
  return props.todasLasMotos
    .filter(m => m.moto.categoria === props.moto.moto.categoria && m.id_ejemplar !== props.moto.id_ejemplar && m.estado_venta === 'Disponible')
    .slice(0, 4);
});

// Bloquear el scroll del fondo
onMounted(() => document.body.style.overflow = 'hidden');
onUnmounted(() => document.body.style.overflow = '');
</script>

<template>
  <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" style="font-family: 'Montserrat', sans-serif;">
    <div class="absolute inset-0 bg-zinc-950/90 backdrop-blur-sm transition-opacity" @click="$emit('close')"></div>

    <div class="relative w-full max-w-6xl max-h-[90vh] overflow-y-auto bg-zinc-950 border border-zinc-800 rounded-3xl shadow-2xl flex flex-col no-scrollbar">
      
      <div class="sticky top-0 z-50 flex items-center justify-between p-4 bg-zinc-950/80 backdrop-blur-md border-b border-zinc-800">
        <p class="text-zinc-400 text-sm font-semibold ml-2">Detalle de la unidad</p>
        <button @click="$emit('close')" class="w-10 h-10 bg-zinc-900 hover:bg-zinc-800 text-zinc-400 hover:text-white rounded-full flex items-center justify-center transition-all border border-zinc-800">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <div class="p-6 sm:p-10">
        <div class="grid lg:grid-cols-2 gap-12 mb-16">
          <div class="space-y-4">
            <div class="relative aspect-[4/3] rounded-3xl overflow-hidden bg-zinc-900 border border-zinc-800">
              <img :src="moto.imagenes[imgActiva]" :alt="`${moto.moto.marca} ${moto.moto.modelo}`" class="w-full h-full object-cover transition-all duration-500" />
              <div v-if="moto.estado_venta !== 'Disponible'" class="absolute inset-0 bg-zinc-950/40 flex items-center justify-center">
                <span :class="['text-lg font-black px-6 py-3 rounded-2xl', estadoColor[moto.estado_venta]]">{{ moto.estado_venta }}</span>
              </div>
              <button v-if="moto.imagenes.length > 1 && imgActiva > 0" @click="imgActiva--" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-zinc-950/70 rounded-full flex items-center justify-center text-white hover:bg-zinc-800 transition-all">←</button>
              <button v-if="moto.imagenes.length > 1 && imgActiva < moto.imagenes.length - 1" @click="imgActiva++" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-zinc-950/70 rounded-full flex items-center justify-center text-white hover:bg-zinc-800 transition-all">→</button>
            </div>
            <div v-if="moto.imagenes.length > 1" class="flex gap-3 overflow-x-auto no-scrollbar pb-2">
              <button v-for="(img, i) in moto.imagenes" :key="i" @click="imgActiva = i" :class="['w-20 h-16 rounded-xl overflow-hidden border-2 transition-all shrink-0', imgActiva === i ? 'border-orange-500' : 'border-zinc-800 hover:border-zinc-600']">
                <img :src="img" class="w-full h-full object-cover" />
              </button>
            </div>
          </div>

          <div>
            <div class="flex items-start justify-between mb-2 text-white">
              <p class="text-orange-400 font-black uppercase tracking-widest text-sm">{{ moto.moto.marca }}</p>
              <span :class="['px-3 py-1 rounded-full text-xs font-bold', estadoColor[moto.estado_venta]]">{{ moto.estado_venta }}</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-black tracking-tighter mb-2 text-white">{{ moto.moto.modelo }}</h1>
            <p class="text-zinc-500 text-lg mb-8">{{ moto.anio_fabricacion }} · {{ moto.moto.cilindrada }} · {{ moto.moto.categoria }}</p>

            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 mb-6">
              <p class="text-xs text-zinc-500 uppercase tracking-widest mb-1">Precio</p>
              <p class="text-4xl font-black text-white">{{ fmt(moto.precio) }}</p>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-8">
              <div v-for="spec in [
                { label: 'Kilometraje', value: moto.km === 0 ? '0 km' : `${moto.km.toLocaleString('es-AR')} km` },
                { label: 'Color', value: moto.color || 'No especificado' },
                { label: 'Cilindrada', value: moto.moto.cilindrada },
                { label: 'Categoría', value: moto.moto.categoria },
                { label: 'Sucursal', value: moto.sucursal },
                { label: 'Dominio', value: moto.dominio ?? 'Sin patentar' },
              ]" :key="spec.label" class="bg-zinc-900/60 border border-zinc-800 rounded-xl p-3 text-white">
                <p class="text-xs text-zinc-500 mb-0.5">{{ spec.label }}</p>
                <p class="text-sm font-bold">{{ spec.value }}</p>
              </div>
            </div>

            <div class="space-y-3">
              <a :href="`https://wa.me/5493434531520?text=${encodeURIComponent(waMsg)}`" target="_blank"
                :class="['w-full flex items-center justify-center gap-3 font-bold py-4 rounded-2xl text-base transition-all active:scale-95', moto.estado_venta === 'Disponible' ? 'bg-orange-500 hover:bg-orange-400 text-white shadow-xl shadow-orange-500/20' : 'bg-zinc-800 text-zinc-500 cursor-not-allowed pointer-events-none']">
                Consultar por WhatsApp
              </a>
            </div>
          </div>
        </div>

        <div v-if="relacionadas.length">
          <h2 class="text-2xl font-black tracking-tight mb-6 text-white">También te puede interesar</h2>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <button v-for="rel in relacionadas" :key="rel.id_ejemplar" @click="$emit('cambiarMoto', rel)"
              class="group bg-zinc-900 rounded-2xl border border-zinc-800 overflow-hidden hover:border-zinc-600 transition-all hover:-translate-y-1 block text-left">
              <div class="aspect-[4/3] overflow-hidden bg-zinc-800">
                <img :src="rel.imagenes[0]" :alt="`${rel.moto.marca} ${rel.moto.modelo}`" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
              </div>
              <div class="p-4">
                <p class="text-xs text-orange-400 font-bold uppercase tracking-widest">{{ rel.moto.marca }}</p>
                <p class="font-black mt-0.5 text-white">{{ rel.moto.modelo }}</p>
                <p class="text-sm text-zinc-400 font-bold mt-1">{{ fmt(rel.precio) }}</p>
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>