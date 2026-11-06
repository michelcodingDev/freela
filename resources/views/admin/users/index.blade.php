<?php use Illuminate\Support\Str; ?>

<x-layout title="Usuários - Admin - FreelaJob">

    <div class="max-w-7xl mx-auto py-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Todos os Usuários</h1>
                <p class="text-gray-600">Gerencie todos os usuários da plataforma.</p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="flex flex-wrap gap-4">
                <div class="relative">
                    <input type="text" placeholder="Buscar por nome ou e-mail..."
                           class="w-full px-4 py-2.5 pl-10 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-sm">
                    <div class="absolute left-3 top-1/2 -mt-2.5">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                    </div>
                </div>
                <select class="px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                    <option>Todos os tipos</option>
                    <option>Cliente</option>
                    <option>Freelancer</option>
                    <option>Admin</option>
                </select>
                <select class="px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                    <option>Todos os status</option>
                    <option>Ativo</option>
                    <option>Inativo</option>
                    <option>Suspenso</option>
                    <option>Banido</option>
                </select>
            </div>
        </div>

        <!-- Tabela de usuários -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Usuário</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">E-mail</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Cadastro</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $user->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}"
                                             class="w-8 h-8 rounded-full object-cover border-2 border-gray-100" alt="Usuário">
                                        <span class="text-sm text-gray-900">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if ($user->type === 'cliente') bg-blue-100 text-blue-700
                                        @elseif ($user->type === 'freelancer') bg-purple-100 text-purple-700
                                        @elseif ($user->type === 'admin') bg-green-100 text-green-700
                                        @endif">
                                        {{ ucfirst($user->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if ($user->status === 'ativo') bg-green-100 text-green-700
                                        @elseif ($user->status === 'inativo') bg-gray-100 text-gray-600
                                        @elseif ($user->status === 'suspenso') bg-amber-100 text-amber-700
                                        @elseif ($user->status === 'banido') bg-red-100 text-red-700
                                        @endif">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('freelancers.show', $user) }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Ver</a>
                                        @if (!$user->isAdmin())
                                            <form method="POST" action="{{ route('admin.users.status', $user) }}" class="flex gap-1">
                                                @csrf
                                                <select name="status" class="text-xs border border-gray-300 rounded-lg px-1 py-1">
                                                    @foreach (['ativo','inativo','suspenso','banido'] as $s)
                                                        <option value="{{ $s }}" {{ $user->status === $s ? 'selected' : '' }}>{{ $s }}</option>
                                                    @endforeach
                                                </select>
                                                <button class="text-xs text-red-600 font-medium hover:text-red-700">Atualizar</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        </div>
    </div>

</x-layout>
