<x-layout title="Logs de Auditoria - Admin - FreelaJob">

    <div class="max-w-7xl mx-auto py-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Logs de Auditoria</h1>
                <p class="text-gray-600">Acompanhe todas as ações realizadas na plataforma.</p>
            </div>
            <a href="{{ route('admin.logs.export') }}"
               class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold flex items-center gap-2 transition-all shadow-sm">
                <i data-lucide="download" class="w-4 h-4"></i>
                Exportar CSV
            </a>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="relative flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Usuário</label>
                    <select name="user_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-sm">
                        <option value="">Todos os usuários</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="relative flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Ação</label>
                    <select name="action" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-sm">
                        <option value="">Todas as ações</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ ucfirst($action) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="relative flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Modelo</label>
                    <select name="model_type" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-sm">
                        <option value="">Todos os modelos</option>
                        @foreach ($modelTypes as $modelType)
                            <option value="{{ $modelType }}" {{ request('model_type') == $modelType ? 'selected' : '' }}>
                                {{ $modelType }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-all shadow-lg">
                    Filtrar
                </button>
                <a href="{{ route('admin.logs.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
                    Limpar
                </a>
            </form>
        </div>

        <!-- Tabela de logs -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Usuário</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Ação</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Modelo</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">ID Modelo</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">IP</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($logs as $log)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-600 font-mono">#{{ $log->id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $log->user?->name ?? 'Sistema' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                                        @if ($log->action === 'criar') bg-green-100 text-green-700
                                        @elseif ($log->action === 'editar') bg-blue-100 text-blue-700
                                        @elseif ($log->action === 'excluir') bg-red-100 text-red-700
                                        @endif">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $log->model_type }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $log->model_id ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 font-mono">{{ $log->ip_address ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $logs->links() }}
            </div>
        </div>
    </div>

</x-layout>
