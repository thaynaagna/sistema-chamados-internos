<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  chamado: Object,
  podeGerenciar: Boolean,
  equipeSuporte: Array,
})

const statusLabel = {
  aberto: 'Aberto',
  em_andamento: 'Em andamento',
  resolvido: 'Resolvido',
  fechado: 'Fechado',
}

const prioridadeLabel = {
  baixa: 'Baixa',
  media: 'Média',
  alta: 'Alta',
}

const acaoLabel = {
  abertura: 'Abertura',
  mudanca_status: 'Mudança de status',
  reatribuicao: 'Reatribuição',
  comentario: 'Comentário',
}

const statusForm = useForm({
  status: props.chamado.status,
  comentario: '',
})

function atualizarStatus() {
  statusForm.patch(route('chamados.status', props.chamado.id), {
    preserveScroll: true,
    onSuccess: () => { statusForm.comentario = '' },
  })
}

const reatribuirForm = useForm({
  responsavel_id: props.chamado.responsavel?.id ?? '',
  comentario: '',
})

function reatribuir() {
  reatribuirForm.patch(route('chamados.reatribuir', props.chamado.id), {
    preserveScroll: true,
    onSuccess: () => { reatribuirForm.comentario = '' },
  })
}

function formatarData(data) {
  return new Date(data).toLocaleString('pt-BR')
}
</script>

<template>
  <Head :title="`Chamado #${chamado.id}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center gap-3">
        <Link :href="route('chamados.index')" class="text-slate-400 hover:text-slate-600">&larr;</Link>
        <h2 class="text-xl font-semibold leading-tight text-slate-800">
          Chamado #{{ chamado.id }} — {{ chamado.titulo }}
        </h2>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto grid max-w-4xl grid-cols-1 gap-6 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">

        <!-- Coluna principal -->
        <div class="space-y-6 lg:col-span-2">

          <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-medium text-slate-500">Descrição</h3>
            <p class="mt-2 whitespace-pre-line text-slate-700">{{ chamado.descricao }}</p>
          </div>

          <!-- Histórico -->
          <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-sm font-medium text-slate-500">Histórico</h3>
            <ol class="space-y-4 border-l border-slate-200 pl-4">
              <li v-for="item in chamado.historico" :key="item.id">
                <div class="text-sm font-medium text-slate-700">
                  {{ acaoLabel[item.acao] ?? item.acao }}
                  <span class="text-slate-400">— {{ item.user.name }}</span>
                </div>
                <div class="text-xs text-slate-400">{{ formatarData(item.created_at) }}</div>
                <div v-if="item.status_anterior || item.status_novo" class="mt-1 text-sm text-slate-600">
                  {{ statusLabel[item.status_anterior] ?? '—' }} → {{ statusLabel[item.status_novo] ?? '—' }}
                </div>
                <p v-if="item.comentario" class="mt-1 text-sm text-slate-600">{{ item.comentario }}</p>
              </li>
            </ol>
          </div>

        </div>

        <!-- Sidebar -->
        <div class="space-y-6">

          <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <dl class="space-y-3 text-sm">
              <div class="flex justify-between">
                <dt class="text-slate-500">Status</dt>
                <dd class="font-medium text-slate-800">{{ statusLabel[chamado.status] }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-slate-500">Prioridade</dt>
                <dd class="font-medium text-slate-800">{{ prioridadeLabel[chamado.prioridade] }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-slate-500">Categoria</dt>
                <dd class="font-medium text-slate-800">{{ chamado.categoria?.nome ?? '—' }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-slate-500">Aberto por</dt>
                <dd class="font-medium text-slate-800">{{ chamado.solicitante.name }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-slate-500">Responsável</dt>
                <dd class="font-medium text-slate-800">{{ chamado.responsavel?.name ?? 'Sem responsável' }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-slate-500">Aberto em</dt>
                <dd class="font-medium text-slate-800">{{ formatarData(chamado.created_at) }}</dd>
              </div>
            </dl>
          </div>

          <!-- Ações de suporte/admin -->
          <div v-if="podeGerenciar" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="mb-3 text-sm font-medium text-slate-500">Atualizar status</h3>
            <form @submit.prevent="atualizarStatus" class="space-y-3">
              <select v-model="statusForm.status" class="block w-full rounded-md border-slate-300 text-sm">
                <option value="aberto">Aberto</option>
                <option value="em_andamento">Em andamento</option>
                <option value="resolvido">Resolvido</option>
                <option value="fechado">Fechado</option>
              </select>
              <textarea
                v-model="statusForm.comentario"
                rows="2"
                placeholder="Comentário (opcional)"
                class="block w-full rounded-md border-slate-300 text-sm"
              ></textarea>
              <button
                type="submit"
                :disabled="statusForm.processing"
                class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 disabled:opacity-50"
              >
                Salvar status
              </button>
            </form>

            <hr class="my-4 border-slate-100" />

            <h3 class="mb-3 text-sm font-medium text-slate-500">Reatribuir responsável</h3>
            <form @submit.prevent="reatribuir" class="space-y-3">
              <select v-model="reatribuirForm.responsavel_id" class="block w-full rounded-md border-slate-300 text-sm">
                <option value="" disabled>Selecione...</option>
                <option v-for="u in equipeSuporte" :key="u.id" :value="u.id">{{ u.name }}</option>
              </select>
              <textarea
                v-model="reatribuirForm.comentario"
                rows="2"
                placeholder="Motivo da reatribuição (opcional)"
                class="block w-full rounded-md border-slate-300 text-sm"
              ></textarea>
              <button
                type="submit"
                :disabled="reatribuirForm.processing"
                class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-50"
              >
                Reatribuir
              </button>
            </form>
          </div>

        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
