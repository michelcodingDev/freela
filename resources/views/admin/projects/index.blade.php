<?php use Illuminate\Support\Str; ?>

<x-layout title="Projetos - Admin - FreelaJob">

    <div class="max-w-7xl mx-auto py-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Todos os Projetos</h1>
                <p class="text-gray-600">Gerencie todos os projetos da plataforma.</p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="flex flex-wrap gap-4">
                <div class="relative">
                    <input type="text" placeholder="Buscar por título..."
                           class="w-full px-4 py-2.5 pl-10 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-sm">
                    <div class="absolute left-3 top-1/2 -mt-2.5">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                    </div>
                </div>
                <select class="px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                    <option>Todos os status</option>
                    <option>Aguardando Aprovação</option>
                    <option>Aguardando Propostas</option>
                    <option>Aguardando Pagamento</option>
                    <option>Em Andamento</option>
                    <option>Aguardando Liberação</option>
                    <option>Concluído</option>
                    <option>Cancelado</option>
                    <option>Disputa</option>
                </select>
            </div>
        </div>

        <!-- Tabela de projetos -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Projeto</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Cliente</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Categoria</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Valor</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Data</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($projects as $project)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">{{ $project->title }}</span>
                                        <p class="text-xs text-gray-500">{{ Str::limit($project->description, 60) }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $project->client->name }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                                        style="background-color: {{ $project->category->color ?? '#64748b' }}20; color: {{ $project->category->color ?? '#64748b' }}">
                                        {{ $project->category->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if ($project->budget_min && $project->budget_max)
                                        R$ {{ number_format($project->budget_min, 2, ',', '.') }} - R$ {{ number_format($project->budget_max, 2, ',', '.') }}
                                    @else
                                        Não informado
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if ($project->status === 'aguardando_aprovacao') bg-amber-100 text-amber-700
                                        @elseif ($project->status === 'aguardando_propostas') bg-blue-100 text-blue-700
                                        @elseif ($project->status === 'aguardando_pagamento') bg-purple-100 text-purple-700
                                        @elseif ($project->status === 'em_andamento') bg-blue-100 text-blue-700
                                        @elseif ($project->status === 'aguardando_liberacao') bg-amber-100 text-amber-700
                                        @elseif ($project->status === 'concluído') bg-green-100 text-green-700
                                        @elseif ($project->status === 'disputa') bg-red-100 text-red-700
                                        @elseif ($project->status === 'fechado') bg-gray-100 text-gray-600
                                        @elseif ($project->status === 'reprovado') bg-red-100 text-red-700
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $project->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('projects.show', $project) }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                        Ver Detalhes →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $projects->links() }}
            </div>
        </div>
    </div>

</x-layout>
