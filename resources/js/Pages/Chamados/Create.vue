<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineProps({
  categorias: Array,
})

const form = useForm({
  titulo: '',
  descricao: '',
  categoria_id: '',
  prioridade: 'media',
})

function submeter() {
  form.post(route('chamados.store'))
}
</script>

<template>
  <Head title="Novo chamado" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-slate-800">Abrir novo chamado</h2>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <form @submit.prevent="submeter" class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

          <div>
            <label class="block text-sm font-medium text-slate-700">Título</label>
            <input
              v-model="form.titulo"
              type="text"
              placeholder="Ex: Impressora do 3º andar não está funcionando"
              class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            />
            <p v-if="form.errors.titulo" class="mt-1 text-sm text-rose-600">{{ form.errors.titulo }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700">Descrição</label>
            <textarea
              v-model="form.descricao"
              rows="5"
              placeholder="Descreva o problema com detalhes: o que aconteceu, desde quando, e qualquer informação que ajude a resolver mais rápido."
              class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            ></textarea>
            <p v-if="form.errors.descricao" class="mt-1 text-sm text-rose-600">{{ form.errors.descricao }}</p>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700">Categoria</label>
              <select v-model="form.categoria_id" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm sm:text-sm">
                <option value="">Selecione...</option>
                <option v-for="c in categorias" :key="c.id" :value="c.id">{{ c.nome }}</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700">Prioridade</label>
              <select v-model="form.prioridade" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm sm:text-sm">
                <option value="baixa">Baixa — pode esperar</option>
                <option value="media">Média — rotina</option>
                <option value="alta">Alta — está travando o trabalho</option>
              </select>
              <p v-if="form.errors.prioridade" class="mt-1 text-sm text-rose-600">{{ form.errors.prioridade }}</p>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 pt-2">
            <Link :href="route('chamados.index')" class="text-sm text-slate-500 hover:text-slate-700">
              Cancelar
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50"
            >
              Abrir chamado
            </button>
          </div>

        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
