<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { reactive } from 'vue'

const props = defineProps({
  chamados: Object,
  filtros: Object,
  categorias: Array,
  equipeSuporte: Array,
  podeGerenciar: Boolean,
})

const filtros = reactive({
  status: props.filtros.status ?? '',
  prioridade: props.filtros.prioridade ?? '',
  responsavel_id: props.filtros.responsavel_id ?? '',
  categoria_id: props.filtros.categoria_id ?? '',
})

function aplicarFiltros() {
  router.get(route('chamados.index'), filtros, { preserveState: true, replace: true })
}

const statusLabel = {
  aberto: 'Aberto',
  em_andamento: 'Em andamento',
  resolvido: 'Resolvido',
  fechado: 'Fechado',
}

const statusCor = {
  aberto: 'bg-amber-100 text-amber-800 border-amber-200',
  em_andamento: 'bg-sky-100 text-sky-800 border-sky-200',
  resolvido: 'bg-emerald-100 text-emerald-800 border-emerald-200',
  fechado: 'bg-slate-100 text-slate-600 border-slate-200',
}

const prioridadeCor = {
  baixa: 'bg-slate-100 text-slate-600',
  media: 'bg-sky-100 text-sky-700',
  alta: 'bg-orange-100 text-orange-700',
}

const prioridadeLabel = {
  baixa: 'Baixa',
  media: 'Média',
  alta: 'Alta',
}
</script>

<template>
  <Head title="Chamados" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-slate-800">
          {{ podeGerenciar ? 'Chamados — Suporte' : 'Meus chamados' }}
        </h2>
        <Link
          :href="route('chamados.create')"
          class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500"
        >
          + Novo chamado
        </Link>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-6xl space-y-4 px-4 sm:px-6 lg:px-8">

        <!-- Filtros (apenas suporte/admin) -->
        <div v-if="podeGerenciar" class="grid grid-cols-2 gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-4">
          <div>
            <label class="block text-xs font-medium text-slate-500">Status</label>
            <select v-model="filtros.status" @change="aplicarFiltros" class="mt-1 w-full rounded-md border-slate-300 text-sm">
              <option value="">Todos</option>
              <option value="aberto">Aberto</option>
              <option value="em_andamento">Em andamento</option>
              <option value="resolvido">Resolvido</option>
              <option value="fechado">Fechado</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500">Prioridade</label>
            <select v-model="filtros.prioridade" @change="aplicarFiltros" class="mt-1 w-full rounded-md border-slate-300 text-sm">
              <option value="">Todas</option>
              <option value="baixa">Baixa</option>
              <option value="media">Média</option>
              <option value="alta">Alta</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500">Categoria</label>
            <select v-model="filtros.categoria_id" @change="aplicarFiltros" class="mt-1 w-full rounded-md border-slate-300 text-sm">
              <option value="">Todas</option>
              <option v-for="c in categorias" :key="c.id" :value="c.id">{{ c.nome }}</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500">Responsável</label>
            <select v-model="filtros.responsavel_id" @change="aplicarFiltros" class="mt-1 w-full rounded-md border-slate-300 text-sm">
              <option value="">Todos</option>
              <option v-for="u in equipeSuporte" :key="u.id" :value="u.id">{{ u.name }}</option>
            </select>
          </div>
        </div>

        <!-- Lista -->
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
          <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-medium uppercase tracking-wide text-slate-500">
              <tr>
                <th class="px-4 py-3">Chamado</th>
                <th class="px-4 py-3">Categoria</th>
                <th class="px-4 py-3">Prioridade</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Responsável</th>
                <th class="px-4 py-3">Aberto em</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="chamado in chamados.data"
                :key="chamado.id"
                class="cursor-pointer hover:bg-slate-50"
                @click="router.visit(route('chamados.show', chamado.id))"
              >
                <td class="px-4 py-3">
                  <div class="font-medium text-slate-800">#{{ chamado.id }} — {{ chamado.titulo }}</div>
                  <div class="text-xs text-slate-400" v-if="podeGerenciar">por {{ chamado.solicitante.name }}</div>
                </td>
                <td class="px-4 py-3 text-slate-500">{{ chamado.categoria?.nome ?? '—' }}</td>
                <td class="px-4 py-3">
                  <span :class="['rounded-full px-2 py-0.5 text-xs font-medium', prioridadeCor[chamado.prioridade]]">
                    {{ prioridadeLabel[chamado.prioridade] }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <span :class="['rounded-full border px-2 py-0.5 text-xs font-medium', statusCor[chamado.status]]">
                    {{ statusLabel[chamado.status] }}
                  </span>
                </td>
                <td class="px-4 py-3 text-slate-600">{{ chamado.responsavel?.name ?? 'Sem responsável' }}</td>
                <td class="px-4 py-3 text-slate-400">{{ new Date(chamado.created_at).toLocaleDateString('pt-BR') }}</td>
              </tr>

              <tr v-if="chamados.data.length === 0">
                <td colspan="6" class="px-4 py-10 text-center text-slate-400">
                  Nenhum chamado encontrado.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginação simples -->
        <div class="flex justify-center gap-2" v-if="chamados.links?.length > 3">
          <Link
            v-for="link in chamados.links"
            :key="link.label"
            :href="link.url ?? '#'"
            v-html="link.label"
            :class="[
              'rounded-md px-3 py-1 text-sm',
              link.active ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100',
              !link.url && 'pointer-events-none opacity-40',
            ]"
          />
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
