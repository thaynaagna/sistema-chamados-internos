<script setup>
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { computed } from 'vue'

const props = defineProps({
  cargas: Array,
  resumo: Object,
})

const maiorCarga = computed(() =>
  Math.max(1, ...props.cargas.map((c) => c.carga_ponderada))
)

function corBarra(carga) {
  const proporcao = carga / maiorCarga.value
  if (proporcao >= 0.8) return 'bg-rose-500'
  if (proporcao >= 0.5) return 'bg-amber-400'
  return 'bg-emerald-500'
}
</script>

<template>
  <Head title="Distribuição de carga" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-slate-800">Distribuição de carga — Suporte</h2>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">

        <!-- Resumo geral -->
        <div class="grid grid-cols-3 gap-4">
          <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-2xl font-semibold text-amber-600">{{ resumo.total_abertos }}</div>
            <div class="text-sm text-slate-500">Aguardando atendimento</div>
          </div>
          <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-2xl font-semibold text-sky-600">{{ resumo.total_em_andamento }}</div>
            <div class="text-sm text-slate-500">Em andamento</div>
          </div>
          <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-2xl font-semibold text-emerald-600">{{ resumo.total_resolvidos_mes }}</div>
            <div class="text-sm text-slate-500">Resolvidos este mês</div>
          </div>
        </div>

        <!-- Carga por pessoa -->
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
          <div class="mb-1 flex items-baseline justify-between">
            <h3 class="text-sm font-medium text-slate-700">Carga atual por pessoa</h3>
            <p class="text-xs text-slate-400">Chamados ativos, ponderados por prioridade</p>
          </div>

          <div v-if="cargas.length === 0" class="py-8 text-center text-slate-400">
            Nenhuma pessoa cadastrada com perfil "suporte" ainda.
          </div>

          <div v-for="pessoa in cargas" :key="pessoa.id" class="mt-4">
            <div class="mb-1 flex items-center justify-between text-sm">
              <span class="font-medium text-slate-700">{{ pessoa.nome }}</span>
              <span class="text-slate-500">
                {{ pessoa.quantidade }} chamado(s) · carga {{ pessoa.carga_ponderada }}
              </span>
            </div>
            <div class="h-2.5 w-full overflow-hidden rounded-full bg-slate-100">
              <div
                :class="['h-full rounded-full transition-all', corBarra(pessoa.carga_ponderada)]"
                :style="{ width: `${(pessoa.carga_ponderada / maiorCarga) * 100}%` }"
              ></div>
            </div>
          </div>

          <p class="mt-6 text-xs text-slate-400">
            A "carga" soma os chamados ativos de cada pessoa, dando peso maior para
            prioridades mais altas (alta = 3, média = 2, baixa = 1).
            Novos chamados são atribuídos automaticamente a quem tiver a menor carga.
          </p>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
