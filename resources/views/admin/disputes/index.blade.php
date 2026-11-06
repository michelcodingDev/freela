<x-layout title="Disputas - Admin - FreelaJob">

    <div class="max-w-7xl mx-auto py-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Disputas</h1>
                <p class="text-gray-600">Gerencie disputas entre clientes e freelancers.</p>
            </div>
            <div class="flex gap-3 text-sm">
                <span class="px-3 py-1.5 rounded-full bg-gray-100 text-gray-700 font-medium">Total: {{ $summary['total'] ?? 0 }}</span>
                <span class="px-3 py-1.5 rounded-full bg-amber-100 text-amber-700 font-medium">Em análise: {{ $summary['open'] ?? 0 }}</span>
                <span class="px-3 py-1.5 rounded-full bg-green-100 text-green-700 font-medium">Resolvidas: {{ $summary['resolved'] ?? 0 }}</span>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.disputes') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
            <div class="flex flex-wrap gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Projeto, cliente, freelancer ou ID..."
                       class="flex-1 min-w-[220px] px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none text-sm">
                <select name="status" class="px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 outline-none">
                    <option value="">Todos os status</option>
                    <option value="aberta" {{ request('status') === 'aberta' ? 'selected' : '' }}>Em análise</option>
                    <option value="resolvida" {{ request('status') === 'resolvida' ? 'selected' : '' }}>Resolvida</option>
                </select>
                <button class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold">Filtrar</button>
                @if (request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.disputes') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">Limpar</a>
                @endif
            </div>
        </form>

        <!-- Tabela de disputas -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Projeto</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Cliente</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Freelancer</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Criado em</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($disputes as $dispute)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-600 font-mono">#{{ $dispute->id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $dispute->project->title }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $dispute->client->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $dispute->freelancer->name }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if ($dispute->status === 'aberta') bg-amber-100 text-amber-700
                                        @elseif ($dispute->status === 'em_analise') bg-blue-100 text-blue-700
                                        @elseif ($dispute->status === 'resolvida') bg-green-100 text-green-700
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $dispute->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $dispute->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('disputes.show', $dispute) }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                        {{ $dispute->status === 'resolvida' ? 'Ver' : 'Resolver' }} →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400">Nenhuma disputa encontrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $disputes->links() }}
            </div>
        </div>
    </div>

</x-layout>
