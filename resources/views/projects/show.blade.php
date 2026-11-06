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
                                    <a href="{{ route('attachments.download', $attachment) }}" target="_blank"
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
                        <div class="mt-6 space-y-2">
                            <a href="{{ route('projects.edit', $project) }}"
                               class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium flex items-center justify-center gap-2 transition-all">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                                Editar Projeto
                            </a>
                            @if ($project->status === 'aguardando_propostas' && !$project->is_featured)
                                <form method="POST" action="{{ route('projects.boost', $project) }}">@csrf<button class="w-full py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-medium">Turbinar — R$ 19,90</button></form>
                            @endif
                            @if ($project->status === 'fechado')
                                <form method="POST" action="{{ route('projects.reopen', $project) }}">@csrf<button class="w-full py-3 bg-gray-700 hover:bg-gray-800 text-white rounded-xl font-medium">Reabrir projeto</button></form>
                            @endif
                        </div>
                    @endif

                    @auth
                        @if ($project->isParticipant(auth()->id()) || auth()->user()->isAdmin())
                            <div class="mt-6 space-y-2">
                                <a href="{{ route('projects.messages.index', $project) }}" class="w-full py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium flex items-center justify-center gap-2">
                                    <i data-lucide="message-circle" class="w-4 h-4"></i> Chat do projeto
                                </a>
                                @if ($project->status === 'aguardando_pagamento')
                                    @if (auth()->user()->isFreelancer())
                                        <form method="POST" action="{{ route('projects.start', $project) }}">@csrf<button class="w-full py-3 bg-blue-600 text-white rounded-xl font-medium">Iniciar execução</button></form>
                                    @elseif (auth()->user()->isCliente())
                                        <a href="{{ route('payments.process', $project) }}" class="w-full py-3 bg-green-600 text-white rounded-xl font-medium flex items-center justify-center">Pagar em custódia</a>
                                    @endif
                                @endif
                                @if ($project->status === 'em_andamento' && auth()->user()->isFreelancer() && auth()->id() === $project->acceptedProposal?->freelancer_id)
                                    <form method="POST" action="{{ route('projects.deliver', $project) }}" class="space-y-2">
                                        @csrf
                                        <label for="delivery_note" class="block text-xs font-medium text-gray-600">Nota da entrega (opcional)</label>
                                        <textarea id="delivery_note" name="delivery_note" maxlength="5000" rows="3"
                                                  class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                                                  placeholder="O que foi entregue, links ou instruções...">{{ old('delivery_note') }}</textarea>
                                        <button class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium">Entregar projeto</button>
                                    </form>
                                @endif
                                @if ($project->status === 'aguardando_liberacao' && auth()->user()->isCliente() && auth()->id() === $project->client_id)
                                    @php $pay = $project->payments()->where('status', 'custodia')->first() @endphp
                                    @if ($pay)
                                        <form method="POST" action="{{ route('payments.release', $pay) }}" onsubmit="return confirm('Liberar pagamento ao freelancer?')">@csrf<button class="w-full py-3 bg-green-600 text-white rounded-xl font-medium">Liberar pagamento</button></form>
                                    @endif
                                @endif
                                @if ($project->status === 'concluido')
                                    @php $alreadyReviewed = $project->reviews->contains('from_user_id', auth()->id()); @endphp
                                    @if ($alreadyReviewed)
                                        <span class="w-full py-3 bg-gray-100 text-gray-500 rounded-xl font-medium flex items-center justify-center gap-2">
                                            <i data-lucide="check-circle" class="w-4 h-4"></i> Você já avaliou
                                        </span>
                                    @else
                                        <a href="{{ route('projects.reviews.create', $project) }}" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-medium flex items-center justify-center gap-2">
                                            <i data-lucide="star" class="w-4 h-4"></i> Avaliar projeto
                                        </a>
                                    @endif
                                @endif
                                @if (in_array($project->status, ['em_andamento', 'aguardando_liberacao'], true))
                                    <a href="{{ route('projects.disputes.create', $project) }}" class="w-full py-3 bg-red-100 hover:bg-red-200 text-red-700 rounded-xl font-medium flex items-center justify-center">Abrir disputa</a>
                                @endif
                            </div>
                        @endif
                        @if (auth()->user()->isAdmin() && $project->status === 'aguardando_aprovacao')
                            <div class="mt-4 flex gap-2">
                                <form method="POST" action="{{ route('projects.approve', $project) }}" class="flex-1">@csrf<button class="w-full py-2 bg-green-600 text-white rounded-xl text-sm font-medium">Aprovar</button></form>
                                <form method="POST" action="{{ route('projects.reject', $project) }}" class="flex-1">@csrf<button class="w-full py-2 bg-red-100 text-red-700 rounded-xl text-sm font-medium">Reprovar</button></form>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        @if ($project->delivered_at || $project->delivery_note)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
                <h2 class="text-lg font-bold text-gray-900 mb-2 flex items-center gap-2">
                    <i data-lucide="package-check" class="w-5 h-5 text-green-600"></i> Entrega do freelancer
                </h2>
                @if ($project->delivered_at)
                    <p class="text-xs text-gray-500">Entregue em {{ $project->delivered_at->format('d/m/Y H:i') }} ({{ $project->delivered_at->diffForHumans() }})</p>
                @endif
                @if ($project->delivery_note)
                    <p class="text-sm text-gray-700 mt-3 leading-relaxed whitespace-pre-wrap">{{ $project->delivery_note }}</p>
                @else
                    <p class="text-sm text-gray-500 mt-3">Sem nota de entrega.</p>
                @endif
            </div>
        @endif

        @php $publicReviews = $project->reviews->where('is_public', true); @endphp
        @if ($publicReviews->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-2">Avaliações do projeto</h2>
                <p class="text-sm text-gray-500 mb-6">{{ $publicReviews->count() }} {{ $publicReviews->count() === 1 ? 'avaliação pública' : 'avaliações públicas' }}</p>

                <div class="space-y-4">
                    @foreach ($publicReviews as $review)
                        <article class="border border-gray-100 rounded-xl p-5 {{ $loop->last ? '' : '' }}">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $review->fromUser?->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($review->fromUser?->name ?? '?').'&background=random&size=64' }}"
                                         class="w-10 h-10 rounded-full object-cover" alt="">
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $review->fromUser?->name ?? 'Usuário' }}</p>
                                        <p class="text-xs text-gray-400">{{ $review->created_at->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <p class="text-amber-500 text-sm" aria-label="{{ $review->rating }} de 5 estrelas">
                                    {{ str_repeat('★', (int) $review->rating) }}<span class="text-gray-200">{{ str_repeat('★', max(0, 5 - (int) $review->rating)) }}</span>
                                </p>
                            </div>
                            @if ($review->comment)
                                <p class="text-sm text-gray-700 mt-3 leading-relaxed">{{ $review->comment }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

</x-layout>
