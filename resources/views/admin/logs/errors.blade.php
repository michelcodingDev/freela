<x-layout title="Logs de Erro - Admin - FreelaJob">

    <div class="max-w-7xl mx-auto py-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Logs de Erro do Sistema</h1>
                <p class="text-gray-600">Acompanhe erros e exceções da aplicação.</p>
            </div>
        </div>

        @if ($errorLogs->isEmpty() || !$errorLogs)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <i data-lucide="check-circle" class="w-12 h-12 text-green-500 mx-auto mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900">Nenhum erro registrado</h3>
                <p class="text-gray-500">O sistema está funcionando normalmente.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Data/Hora</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Nível</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Mensagem</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($errorLogs as $log)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-sm text-gray-600 font-mono">
                                        {{ substr($log, 0, 20) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            ERROR
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 max-w-[600px] truncate">
                                        {{ substr($log, 20) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

</x-layout>
