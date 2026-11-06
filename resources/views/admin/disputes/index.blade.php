<x-layout title="Disputas - Admin - FreelaJob">

    <div class="max-w-7xl mx-auto py-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Disputas</h1>
                <p class="text-gray-600">Gerencie disputas entre clientes e freelancers.</p>
            </div>
        </div>

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
                        @foreach ($disputes as $dispute)
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
                                    <a href="#" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                        Resolver →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
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
