<?php use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; ?>

<x-layout title="{{ $project->title }} - FreelaJob">

    <div class="max-w-4xl mx-auto py-8">
        <!-- Header do projeto -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">
                            <i data-lucide="clock" class="w-3 h-3"></i>
                            {{ $project->proposal_deadline->format('d/m/Y') }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">
                            <i data-lucide="briefcase" class="w-3 h-3"></i>
                            {{ $project->experience_level }}
                        </span>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $project->title }}</h1>

                    <div class="flex gap-4 text-sm text-gray-600">
                        <span><i data-lucide="user" class="w-3 h-3 mr-1"></i> {{ $project->client->name }}</span>
                        <span><i data-lucide="category" class="w-3 h-3 mr-1"></i> {{ $project->category->name }}</span>
                        @if ($project->budget_min && $project->budget_max)
                            <span><i data-lucide="dollar-sign" class="w-3 h-3 mr-1"></i> R$ {{ number_format($project->budget_min, 2, ',', '.') }} - R$ {{ number_format($project->budget_max, 2, ',', '.') }}</span>
                        @endif
                    </div>

                    <div class="mt-6">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Descrição</h3>
                        <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                            {{ $project->description }}
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    @auth
                        @if (auth()->user()->isFreelancer())
                            @if (!$project->proposals->where('freelancer_id', auth()->id())->isEmpty())
                                <button disabled
                                        class="w-full py-4 bg-gray-200 text-gray-500 rounded-xl font-semibold flex items-center justify-center gap-2 cursor-not-allowed">
                                                <i data-lucide="check-circle" class="w-5 h-5"></i>
                                                Proposta Enviada
                                            </button>
                            @else
                                <a href="{{ route('proposals.create', $project) }}"
                                   class="w-full py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold flex items-center justify-center gap-2 transition-all shadow-lg transform active:scale-95">
                                    <i data-lucide="send" class="w-5 h-5"></i>
                                    Enviar Proposta
                                </a>
                            @endif
                        @elseif (auth()->user()->isCliente() && auth()->id() === $project->client_id)
                            <a href="{{ route('projects.edit', $project) }}"
                               class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold flex items-center justify-center gap-2 transition-all shadow-lg">
                                <i data-lucide="edit" class="w-5 h-5"></i>
                                Editar Projeto
                            </a>

                            <form method="POST" action="{{ route('projects.close', $project) }}"
                                  onsubmit="return confirm('Tem certeza que deseja fechar este projeto?')"
                                  class="w-full">
                                @csrf
                                <button type="submit"
                                        class="w-full py-4 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold flex items-center justify-center gap-2 transition-all shadow-lg">
                                    <i data-lucide="x-circle" class="w-5 h-5"></i>
                                    Fechar Projeto
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        <!-- Habilidades e anexos -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <!-- Habilidades -->
                @if ($project->skills->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Habilidades Necessárias</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($project->skills as $skill)
                                <span class="px-3 py-1.5 rounded-full bg-blue-50 text-blue-700 text-sm font-medium border border-blue-100">
                                    {{ $skill->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Anexos -->
                @if ($project->attachments->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Anexos do Projeto</h3>
                        <div class="space-y-3">
                            @foreach ($project->attachments as $attachment)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <div class="flex items-center gap-3">
                                        <i data-lucide="paperclip" class="w-4 h-4 text-gray-400"></i>
                                        <span class="text-sm text-gray-700">{{ $attachment->original_name }}</span>
                                    </div>
                                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank"
                                       class="text-sm text-green-600 hover:text-green-700 font-medium">
                                        Baixar
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Informações</h3>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-500">Status</span>
                            <span class="text-sm font-medium text-green-600 capitalize">
                                {{ str_replace('_', ' ', $project->status) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-500">Visibilidade</span>
                            <span class="text-sm font-medium text-gray-700 capitalize">
                                {{ $project->visibility }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-500">Propostas recebidas</span>
                            <span class="text-sm font-medium text-gray-900">{{ $project->proposals->count() }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-500">Data limite</span>
                            <span class="text-sm font-medium text-gray-900">{{ $project->proposal_deadline->format('d/m/Y') }}</span>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Cliente</h4>
                            <div class="flex items-center gap-3">
                                <img src="{{ $project->client->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($project->client->name) . '&background=random' }}"
                                     class="w-12 h-12 rounded-full object-cover border-2 border-gray-100" alt="Cliente">
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $project->client->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $project->client->type }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (auth()->check() && auth()->user()->isCliente() && auth()->id() === $project->client_id)
                        <div class="mt-6">
                            <a href="{{ route('projects.edit', $project) }}"
                               class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium flex items-center justify-center gap-2 transition-all">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                                Editar Projeto
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-layout>
