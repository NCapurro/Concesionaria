<script setup>
import { ref, onMounted, onUnmounted, computed} from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import MotoModal from '@/Components/MotoModal.vue';


const props = defineProps({
  motos: { type: Array, default: () => [] },
  sucursales: {
    type: Array, default: () => [
      { nombre: 'Paraná', direccion: 'Cura Alvarez y Echague', telefono: '+54 9 343 453-1522', whatsapp: '5493434531522', instagram: 'ncsoftware_dev', lat: -31.7396522, lng: -60.5203775 },
      { nombre: 'Santa Fe', direccion: 'Mendoza 740', telefono: '+54 9 3434 531522', whatsapp: '5493434531522', instagram: 'ncsoftware_dev', lat: -31.646500596262143, lng: -60.71655290567032 }, 
    ]
  }
});

const motoSeleccionada = ref(null);

// Navbar scroll
const scrolled = ref(false);
const activeSection = ref('hero');
const navOpen = ref(false);

function onScroll() {
  scrolled.value = window.scrollY > 60;
  const sections = ['hero', 'catalogo', 'financiacion', 'leads' , 'sucursales', 'contacto'];
  for (const id of [...sections].reverse()) {
    const el = document.getElementById(id);
    if (el && window.scrollY >= el.offsetTop - 100) {
      activeSection.value = id;
      break;
    }
  }
}

onMounted(() => window.addEventListener('scroll', onScroll));
onUnmounted(() => window.removeEventListener('scroll', onScroll));

function scrollTo(id) {
  navOpen.value = false;
  document.getElementById(id)?.scrollIntoView({ behavior: 'smooth' });
}

// Catálogo filters
const catFiltro = ref('Todos');
const estadoFiltro = ref('Todos');
const motos = ref(props.motos.length ? props.motos : []);

const categorias = ['Todos', ...new Set(motos.value.map(m => m.moto.categoria))];

const motosFiltradas = computed(() =>
  motos.value.filter(m =>
    (catFiltro.value === 'Todos' || m.moto.categoria === catFiltro.value) &&
    (estadoFiltro.value === 'Todos' || m.estado_venta === estadoFiltro.value)
  )
);

function fmt(n) {
  return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS', maximumFractionDigits: 0 }).format(n);
}

// Cuotas simulator
const monto = ref(5000000);
const cuotas = ref(12);
const tasa = ref(8);
const cuotaMensual = computed(() => {
  const r = tasa.value / 100 / 12;
  return r === 0 ? monto.value / cuotas.value : monto.value * r / (1 - Math.pow(1 + r, -cuotas.value));
});

// Formulario de Leads
const formLead = useForm({
  nombre: '',
  telefono: '',
  email: '',
  interes: 'Comprar 0km',
  mensaje: ''
});

const enviarLead = () => {
  // Usamos el método post de Inertia integrado en useForm
  formLead.post(route('leads.store'), {
    preserveScroll: true, // Para que la página no salte hacia arriba
    onSuccess: () => {
      // Si todo salió bien, limpiamos el formulario y avisamos
      formLead.reset();
      alert('¡Gracias! Tus datos fueron enviados. Un asesor se contactará a la brevedad.');
    },
    onError: (errors) => {
      console.error(errors);
      alert('Hubo un error al enviar el formulario. Revisá los datos.');
    }
  });
};

// Agregá esto en tu <script setup>
const motoDestacada = computed(() => motos.value.length > 0 ? motos.value[0] : null);
</script>

<template>
  <Head title="MotoShop — Concesionaria de Motos" />

  <nav :class="['fixed top-0 left-0 right-0 z-50 transition-all duration-300', scrolled ? 'bg-zinc-950/95 backdrop-blur border-b border-zinc-800 py-3' : 'bg-transparent py-5']">
    <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
      <button @click="scrollTo('hero')" class="flex items-center gap-3">
        <div class="w-9 h-9 bg-orange-500 rounded-xl flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
          </svg>
        </div>
        <span class="font-black text-white text-lg tracking-tight leading-none">MotoShop<span class="text-orange-500">.</span></span>
      </button>

      <div class="hidden md:flex items-center gap-1">
        <button v-for="item in [['hero','Inicio'],['catalogo','Catálogo'],['financiacion','Financiación'],['leads','Leads'],['sucursales','Sucursales'],['contacto','Contacto']]"
          :key="item[0]" @click="scrollTo(item[0])"
          :class="['px-4 py-2 rounded-lg text-sm font-medium transition-all', activeSection === item[0] ? 'text-orange-400' : 'text-zinc-400 hover:text-white']">
          {{ item[1] }}
        </button>
      </div>

      <div class="hidden md:flex items-center gap-3">
        <Link href="/login" class="text-sm text-zinc-400 hover:text-white transition-colors">Admin</Link>
        <a href="https://wa.me/5493434531522" target="_blank"
          class="flex items-center gap-2 bg-orange-500 hover:bg-orange-400 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-all active:scale-95">
          WhatsApp
        </a>
      </div>

      <button @click="navOpen = !navOpen" class="md:hidden text-zinc-400 hover:text-white">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path v-if="!navOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <div v-if="navOpen" class="md:hidden bg-zinc-950 border-t border-zinc-800 px-6 py-4 flex flex-col gap-2">
      <button v-for="item in [['hero','Inicio'],['catalogo','Catálogo'],['financiacion','Financiación'],['sucursales','Sucursales'],['contacto','Contacto']]"
        :key="item[0]" @click="scrollTo(item[0])"
        class="text-left px-3 py-2 text-zinc-300 hover:text-white hover:bg-zinc-900 rounded-lg text-sm font-medium transition-all">
        {{ item[1] }}
      </button>
    </div>
  </nav>

  <div class="bg-zinc-950 text-white overflow-x-hidden" style="font-family: 'Montserrat', sans-serif;">

    <section id="hero" class="relative min-h-screen flex items-center overflow-hidden">
      <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-950"></div>
        <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-orange-500/5 to-transparent"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-orange-600/8 rounded-full blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.03]"
          style="background-image: linear-gradient(rgba(255,255,255,.8) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.8) 1px, transparent 1px); background-size: 60px 60px;"></div>
      </div>

      <div class="relative max-w-7xl mx-auto px-6 pt-28 pb-20 grid lg:grid-cols-2 gap-16 items-center">
        <div>
          <div class="inline-flex items-center gap-2 bg-orange-500/10 border border-orange-500/20 text-orange-400 text-xs font-semibold px-4 py-2 rounded-full mb-8 tracking-widest uppercase">
            <span class="w-1.5 h-1.5 bg-orange-400 rounded-full animate-pulse"></span>
            Stock disponible hoy
          </div>

          <h1 class="text-5xl md:text-6xl lg:text-7xl font-black tracking-tighter leading-[0.95] mb-6">
            Tu moto<br>
            <span class="text-orange-500">ideal.</span><br>
            <span class="text-zinc-500">A tu precio.</span>
          </h1>

          <p class="text-zinc-400 text-lg leading-relaxed mb-10 max-w-lg">
            0 km y usadas de todas las marcas. Financiación solo con DNI.
            Sucursales en <strong class="text-white">Paraná</strong> y <strong class="text-white">Santa Fe</strong>.
          </p>

          <div class="flex flex-wrap gap-4">
            <button @click="scrollTo('catalogo')"
              class="bg-orange-500 hover:bg-orange-400 text-white font-bold px-8 py-4 rounded-2xl text-base transition-all hover:shadow-xl hover:shadow-orange-500/20 active:scale-95">
              Ver catálogo →
            </button>
            <button @click="scrollTo('financiacion')"
              class="border border-zinc-700 hover:border-zinc-500 text-zinc-300 hover:text-white font-semibold px-8 py-4 rounded-2xl text-base transition-all">
              Simular cuotas
            </button>
          </div>

          <div class="mt-14 flex flex-wrap gap-6 md:gap-10 border-t border-zinc-800 pt-8 justify-between sm:justify-start">
            <div>
              <p class="text-3xl font-black text-white">{{ motos.filter(m => m.km === 0).length }}+</p>
              <p class="text-xs text-zinc-500 uppercase tracking-widest mt-1">Motos 0km</p>
            </div>
            <div class="w-px bg-zinc-800"></div>
            <div>
              <p class="text-3xl font-black text-white">2</p>
              <p class="text-xs text-zinc-500 uppercase tracking-widest mt-1">Sucursales</p>
            </div>
            <div class="w-px bg-zinc-800"></div>
            <div>
              <p class="text-3xl font-black text-orange-400">DNI</p>
              <p class="text-xs text-zinc-500 uppercase tracking-widest mt-1">Solo con DNI</p>
            </div>
          </div>
        </div>

        <div class="relative hidden lg:block" v-if="motoDestacada">
          <div class="relative rounded-3xl overflow-hidden aspect-[4/3] border border-zinc-800">
            <img :src="motoDestacada.imagenes[0]"
              :alt="`${motoDestacada.moto.marca} ${motoDestacada.moto.modelo}`" 
              class="w-full h-full object-cover" />
              
            <div class="absolute inset-0 bg-gradient-to-t from-zinc-950/60 to-transparent"></div>
            
            <div class="absolute bottom-6 left-6 right-6 bg-zinc-950/80 backdrop-blur-sm rounded-2xl p-4 border border-zinc-700/50">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs text-orange-400 font-semibold uppercase tracking-widest">{{ motoDestacada.moto.marca }}</p>
                  <p class="text-lg font-black">{{ motoDestacada.moto.modelo }}</p>
                </div>
                <div class="text-right">
                  <p class="text-xs text-zinc-500">desde</p>
                  <p class="text-xl font-black text-orange-400">{{ fmt(motoDestacada.precio) }}</p>
                </div>
              </div>
            </div>
          </div>
          <div class="absolute -top-4 -right-4 w-24 h-24 border-2 border-orange-500/20 rounded-3xl"></div>
          <div class="absolute -bottom-4 -left-4 w-16 h-16 bg-orange-500/10 rounded-2xl"></div>
        </div>


      </div>

      <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-zinc-600">
        
        <div class="w-px h-8 bg-gradient-to-b from-zinc-600 to-transparent animate-pulse"></div>
      </div>
    </section>

    <section id="catalogo" class="py-24 bg-zinc-950">
      <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-12">
          <div>
            <p class="text-orange-500 text-xs font-bold tracking-widest uppercase mb-3">Nuestro stock</p>
            <h2 class="text-4xl md:text-5xl font-black tracking-tighter">Catálogo<br><span class="text-zinc-600">de motos</span></h2>
          </div>
          <Link :href="route('motos.index')" class="inline-flex items-center gap-2 text-sm text-zinc-400 hover:text-orange-400 transition-colors border border-zinc-800 hover:border-zinc-600 px-4 py-2.5 rounded-xl">
            Ver todo el stock →
          </Link>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 mb-10">
          
          <div class="flex items-center gap-1 bg-zinc-900 border border-zinc-800 rounded-2xl p-1 overflow-x-auto no-scrollbar w-full sm:w-auto">
            <button v-for="cat in categorias" :key="cat" @click="catFiltro = cat"
              :class="['px-4 py-2 rounded-xl text-sm font-semibold transition-all whitespace-nowrap', catFiltro === cat ? 'bg-orange-500 text-white' : 'text-zinc-500 hover:text-white']">
              {{ cat }}
            </button>
          </div>
          
          <div class="flex items-center justify-between gap-3 w-full sm:w-auto sm:ml-auto">
            <select v-model="estadoFiltro"
              class="bg-zinc-900 border border-zinc-800 text-zinc-400 text-sm rounded-2xl px-4 py-2.5 focus:outline-none focus:border-orange-500 w-full sm:w-auto appearance-none">
              <option value="Todos">Todos los estados</option>
              <option value="Disponible">Disponible</option>
              <option value="Reservado">Reservado</option>
            </select>
            <span class="text-zinc-600 text-sm font-medium whitespace-nowrap">{{ motosFiltradas.length }} unidades</span>
          </div>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <button v-for="moto in motosFiltradas.slice(0,6)" :key="moto.id_ejemplar"
  @click="motoSeleccionada = moto"
  class="group bg-zinc-900 rounded-2xl border border-zinc-800 overflow-hidden hover:border-zinc-600 transition-all duration-300 hover:-translate-y-1.5 hover:shadow-2xl hover:shadow-black/50 block text-left w-full">
            <div class="relative aspect-[4/3] overflow-hidden bg-zinc-800">
              <img :src="moto.imagenes[0]" :alt="`${moto.moto.marca} ${moto.moto.modelo}`"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy"/>
              <div v-if="moto.estado_venta !== 'Disponible'" class="absolute inset-0 bg-zinc-950/50"></div>
              <span :class="['absolute top-3 left-3 px-2.5 py-1 rounded-full text-xs font-bold',
                moto.estado_venta === 'Disponible' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' :
                moto.estado_venta === 'Reservado' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' :
                'bg-red-500/20 text-red-400 border border-red-500/30']">
                {{ moto.estado_venta }}
              </span>
              <span :class="['absolute top-3 right-3 px-2.5 py-1 rounded-full text-xs font-bold', moto.km === 0 ? 'bg-orange-500 text-white' : 'bg-zinc-700/80 text-zinc-300']">
                {{ moto.km === 0 ? '0 km' : 'Usada' }}
              </span>
            </div>

            <div class="p-5">
              <p class="text-xs font-bold text-orange-400 uppercase tracking-widest">{{ moto.moto.marca }}</p>
              <div class="flex items-center justify-between mt-1 mb-3">
                <h3 class="text-xl font-black tracking-tight">{{ moto.moto.modelo }}</h3>
                <span class="text-xs bg-zinc-800 text-zinc-400 px-2 py-1 rounded-lg border border-zinc-700">{{ moto.moto.cilindrada }}</span>
              </div>
              <div class="flex flex-wrap gap-2 mb-4">
                <span class="text-xs text-zinc-500 bg-zinc-800/60 px-2.5 py-1 rounded-lg">{{ moto.moto.categoria }}</span>
                
                <span class="text-xs text-zinc-500 bg-zinc-800/60 px-2.5 py-1 rounded-lg">{{ moto.anio_fabricacion }}</span>
              </div>
              <div class="flex items-center justify-between pt-4 border-t border-zinc-800">
                <div>
                  <p class="text-xs text-zinc-600 mb-0.5">Precio</p>
                  <p class="text-2xl font-black tracking-tight">{{ fmt(moto.precio) }}</p>
                </div>
                <span class="text-xs text-orange-400 font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                  Ver detalle <span>→</span>
                </span>
              </div>
            </div>
          </button>
        </div>

        <div v-if="motosFiltradas.length > 6" class="mt-10 text-center">
          <Link :href="route('motos.index')"
            class="inline-flex items-center gap-2 border border-zinc-700 hover:border-orange-500 text-zinc-300 hover:text-orange-400 font-semibold px-8 py-3 rounded-2xl transition-all text-sm">
            Ver las {{ motosFiltradas.length - 6 }} motos restantes →
          </Link>
        </div>
      </div>
    </section>

    <section id="financiacion" class="py-24 bg-zinc-900 relative overflow-hidden">
      <div class="absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-orange-500/5 to-transparent pointer-events-none"></div>

      <div class="max-w-7xl mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
          <div>
            <p class="text-orange-500 text-xs font-bold tracking-widest uppercase mb-3">Financiación</p>
            <h2 class="text-4xl md:text-5xl font-black tracking-tighter mb-6">Solo con<br><span class="text-orange-500">tu DNI.</span></h2>
            <p class="text-zinc-400 text-lg leading-relaxed mb-10">
              Sin recibos de sueldo, sin garante. Llevate tu moto hoy con cuotas fijas en pesos. Aprobación en el momento.
            </p>

            <div class="grid grid-cols-2 gap-4">
              <div v-for="item in [
                { icon: '⚡', titulo: 'Aprobación inmediata', desc: 'Respuesta al instante' },
                { icon: '📄', titulo: 'Solo DNI', desc: 'Sin papelerío' },
                { icon: '📅', titulo: 'Hasta 36 cuotas', desc: 'Cuotas fijas en $' },
                { icon: '🏍️', titulo: 'Todas las marcas', desc: 'Honda, Yamaha, Kawasaki y más' },
              ]" :key="item.titulo"
                class="bg-zinc-800/50 border border-zinc-700/50 rounded-2xl p-4">
                <span class="text-2xl">{{ item.icon }}</span>
                <p class="text-sm font-bold mt-2">{{ item.titulo }}</p>
                <p class="text-xs text-zinc-500 mt-0.5">{{ item.desc }}</p>
              </div>
            </div>
          </div>

          <div class="bg-zinc-950 border border-zinc-800 rounded-3xl p-8">
            <p class="text-sm font-bold text-zinc-400 uppercase tracking-widest mb-6">Simulador de cuotas</p>
            <div class="space-y-6">
              <div>
                <div class="flex justify-between items-center mb-3">
                  <label class="text-sm text-zinc-400">Monto del préstamo</label>
                  <span class="text-orange-400 font-black text-lg">{{ fmt(monto) }}</span>
                </div>
                <input type="range" v-model="monto" min="500000" max="30000000" step="100000"
                  class="w-full accent-orange-500 h-1.5" />
                <div class="flex justify-between text-xs text-zinc-600 mt-1">
                  <span>$500.000</span><span>$30.000.000</span>
                </div>
              </div>

              <div>
                <div class="flex justify-between items-center mb-3">
                  <label class="text-sm text-zinc-400">Cantidad de cuotas</label>
                  <span class="text-orange-400 font-black text-lg">{{ cuotas }} cuotas</span>
                </div>
                <div class="flex gap-2 flex-wrap">
                  <button v-for="c in [6, 12, 18, 24, 36]" :key="c" @click="cuotas = c"
                    :class="['px-4 py-2 rounded-xl text-sm font-bold transition-all', cuotas === c ? 'bg-orange-500 text-white' : 'bg-zinc-800 text-zinc-400 hover:bg-zinc-700']">
                    {{ c }}
                  </button>
                </div>
              </div>

              <div>
                <div class="flex justify-between items-center mb-3">
                  <label class="text-sm text-zinc-400">TNA estimada</label>
                  <span class="text-orange-400 font-black text-lg">{{ tasa }}%</span>
                </div>
                <input type="range" v-model="tasa" min="0" max="120" step="1" class="w-full accent-orange-500 h-1.5" />
              </div>

              <div class="bg-orange-500/10 border border-orange-500/20 rounded-2xl p-6 text-center mt-2">
                <p class="text-xs text-orange-400 font-bold uppercase tracking-widest mb-1">Cuota mensual estimada</p>
                <p class="text-4xl font-black text-white">{{ fmt(Math.round(cuotaMensual)) }}</p>
                <p class="text-xs text-zinc-500 mt-2">* Valor estimado, sujeto a aprobación crediticia</p>
              </div>

              <a href="https://wa.me/5493434531522?text=Hola!%20Quiero%20consultar%20por%20financiación"
                target="_blank"
                class="w-full flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-400 text-white font-bold py-4 rounded-2xl transition-all text-sm active:scale-95">
                Consultar financiación por WhatsApp
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="leads" class="py-24 bg-zinc-950 border-y border-zinc-900 relative overflow-hidden">
      <div class="absolute top-1/2 left-0 w-96 h-96 bg-orange-600/5 rounded-full blur-3xl -translate-y-1/2 -translate-x-1/2 pointer-events-none"></div>

      <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
        
        <div class="relative z-10">
          <div class="inline-flex items-center gap-2 bg-zinc-900 border border-zinc-800 text-zinc-300 text-xs font-semibold px-4 py-2 rounded-full mb-6 uppercase tracking-widest">
            <span class="text-orange-500">★</span> Atención VIP
          </div>
          <h2 class="text-4xl md:text-5xl font-black tracking-tighter mb-6">¿Te ayudamos a<br><span class="text-zinc-600">elegir tu moto?</span></h2>
          <p class="text-zinc-400 text-lg leading-relaxed mb-8 max-w-md">
            Dejanos tus datos y un asesor se va a contactar con vos a la brevedad para armarte un presupuesto a medida y mostrarte el stock oculto.
          </p>
          
          <ul class="space-y-4">
            <li class="flex items-center gap-3 text-sm font-medium text-zinc-300">
              <div class="w-6 h-6 rounded-full bg-orange-500/20 text-orange-500 flex items-center justify-center shrink-0">✓</div>
              Cotizaciones en el acto.
            </li>
            <li class="flex items-center gap-3 text-sm font-medium text-zinc-300">
              <div class="w-6 h-6 rounded-full bg-orange-500/20 text-orange-500 flex items-center justify-center shrink-0">✓</div>
              Tasación de motos usadas.
            </li>
            <li class="flex items-center gap-3 text-sm font-medium text-zinc-300">
              <div class="w-6 h-6 rounded-full bg-orange-500/20 text-orange-500 flex items-center justify-center shrink-0">✓</div>
              Pre-aprobación crediticia.
            </li>
          </ul>
        </div>

        <div class="bg-zinc-900/80 backdrop-blur-xl border border-zinc-800 p-8 sm:p-10 rounded-3xl shadow-2xl relative z-10">
          <form @submit.prevent="enviarLead" class="space-y-5">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              <div class="space-y-1.5">
                <label class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Nombre completo</label>
                <input v-model="formLead.nombre" type="text" required placeholder="Ej. Juan Pérez"
                  class="w-full bg-zinc-950 border border-zinc-800 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all placeholder:text-zinc-700" />
              </div>
              
              <div class="space-y-1.5">
                <label class="text-xs font-bold text-zinc-400 uppercase tracking-widest">WhatsApp</label>
                <input v-model="formLead.telefono" type="tel" required placeholder="Ej. 343 155..."
                  class="w-full bg-zinc-950 border border-zinc-800 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all placeholder:text-zinc-700" />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              <div class="space-y-1.5">
                <label class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Email</label>
                <input v-model="formLead.email" type="email" placeholder="tu@email.com"
                  class="w-full bg-zinc-950 border border-zinc-800 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all placeholder:text-zinc-700" />
              </div>

              <div class="space-y-1.5">
                <label class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Me interesa...</label>
                <select v-model="formLead.interes"
                  class="w-full bg-zinc-950 border border-zinc-800 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all appearance-none">
                  <option value="Comprar 0km">Comprar una 0km</option>
                  <option value="Comprar Usada">Comprar una Usada</option>
                  <option value="Vender mi moto">Vender mi moto actual</option>
                  <option value="Financiación">Consultar financiación</option>
                </select>
              </div>
            </div>

            <div class="space-y-1.5">
              <label class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Mensaje (Opcional)</label>
              <textarea v-model="formLead.mensaje" rows="3" placeholder="Contanos qué modelo buscás o qué dudas tenés..."
                class="w-full bg-zinc-950 border border-zinc-800 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all placeholder:text-zinc-700 resize-none"></textarea>
            </div>

            <button type="submit" :disabled="formLead.processing"
              class="w-full bg-orange-500 hover:bg-orange-400 text-white font-black text-sm uppercase tracking-widest py-4 rounded-xl transition-all active:scale-95 mt-2 flex items-center justify-center gap-2">
              <span v-if="!formLead.processing">Solicitar Asesoramiento</span>
              <span v-else>Enviando...</span>
              <svg v-if="!formLead.processing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
            <p class="text-center text-xs text-zinc-600 mt-4">Tus datos están seguros y no enviamos spam.</p>

          </form>
        </div>
      </div>
    </section>

    <section id="sucursales" class="py-24 bg-zinc-950">
      <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-14">
          <p class="text-orange-500 text-xs font-bold tracking-widest uppercase mb-3">Dónde encontrarnos</p>
          <h2 class="text-4xl md:text-5xl font-black tracking-tighter">Nuestras<br><span class="text-zinc-600">sucursales</span></h2>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
          <div v-for="suc in props.sucursales" :key="suc.nombre"
            class="bg-zinc-900 border border-zinc-800 rounded-3xl overflow-hidden hover:border-zinc-600 transition-all group">

            <div class="aspect-[16/7] bg-zinc-800 relative overflow-hidden">
              <iframe
                :src="`https://maps.google.com/maps?q=${suc.lat},${suc.lng}&z=15&output=embed`"
                class="w-full h-full border-0 grayscale opacity-80 group-hover:opacity-100 group-hover:grayscale-0 transition-all duration-500"
                loading="lazy" referrerpolicy="no-referrer-when-downgrade">
              </iframe>
            </div>

            <div class="p-7">
              <h3 class="text-2xl font-black mb-1">{{ suc.nombre }}</h3>
              <p class="text-zinc-500 text-sm mb-6">{{ suc.direccion }}</p>

              <div class="flex flex-wrap gap-3">
                <a :href="`tel:${suc.telefono}`"
                  class="flex items-center gap-2 bg-zinc-800 hover:bg-zinc-700 text-sm text-zinc-300 px-4 py-2.5 rounded-xl transition-all font-medium">
                  Llamar
                </a>
                <a :href="`https://wa.me/${suc.whatsapp}`" target="_blank"
                  class="flex items-center gap-2 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 text-sm px-4 py-2.5 rounded-xl transition-all font-medium border border-emerald-600/20">
                  WhatsApp
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="contacto" class="py-24 bg-zinc-900 relative overflow-hidden">
      <div class="absolute inset-0 pointer-events-none">
        <div class="absolute bottom-0 left-1/3 w-96 h-96 bg-orange-600/5 rounded-full blur-3xl"></div>
      </div>

      <div class="max-w-3xl mx-auto px-6 text-center">
        <p class="text-orange-500 text-xs font-bold tracking-widest uppercase mb-3">¿Tenés dudas?</p>
        <h2 class="text-4xl md:text-5xl font-black tracking-tighter mb-4">Hablemos<br><span class="text-zinc-600">ahora mismo.</span></h2>
        <p class="text-zinc-400 text-lg mb-12">Respondemos al instante por WhatsApp. Sin vueltas.</p>

        <div class="grid sm:grid-cols-2 gap-4 mb-10">
          <a v-for="suc in props.sucursales" :key="suc.nombre"
            :href="`https://wa.me/${suc.whatsapp}?text=Hola!%20Consulto%20por%20una%20moto`" target="_blank"
            class="flex items-center gap-4 bg-zinc-950 border border-zinc-800 hover:border-emerald-600/40 rounded-2xl p-5 transition-all hover:-translate-y-1 group text-left">
            <div class="w-12 h-12 bg-emerald-600/20 rounded-2xl flex items-center justify-center shrink-0 group-hover:bg-emerald-600/30 transition-all">
              <svg class="w-6 h-6 text-emerald-400" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/></svg>
            </div>
            <div>
              <p class="font-bold text-white">Sucursal {{ suc.nombre }}</p>
              <p class="text-sm text-zinc-500">{{ suc.telefono }}</p>
            </div>
          </a>
        </div>
      </div>
    </section>

    <footer class="bg-zinc-950 border-t border-zinc-900 py-10">
      <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-3">
          <span class="font-black text-white">MotoShop<span class="text-orange-500">.</span></span>
        </div>
        <p class="text-zinc-600 text-sm">© {{ new Date().getFullYear() }} MotoShop. Todos los derechos reservados.</p>
      </div>
    </footer>
  </div>

   <MotoModal 
    v-if="motoSeleccionada" 
    :moto="motoSeleccionada" 
    :todasLasMotos="motos"
    @close="motoSeleccionada = null"
    @cambiarMoto="nuevaMoto => motoSeleccionada = nuevaMoto"
  />

  
</template>
<style>
/* Importamos la fuente Inter (o Montserrat) */
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap');

/* Ocultar scrollbar para Chrome, Safari y Opera */
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
/* Ocultar scrollbar para IE, Edge y Firefox */
.no-scrollbar {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
</style>