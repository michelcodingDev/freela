<x-layout title="Meus Planos - FreelaJob">

    <div class="max-w-5xl mx-auto py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Escolha seu Plano</h1>
            <p class="text-gray-600">Contrate um plano para ter mais benefícios na plataforma.</p>
        </div>

        @if ($currentSubscription)
            <div class="bg-green-50 rounded-2xl p-6 border border-green-100 mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Seu Plano Atual</h2>
                        <p class="text-green-700 text-sm">{{ $currentSubscription->plan->name }}</p>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                        <i data-lucide="check-circle" class="w-3 h-3"></i>
                        Ativo até {{ $currentSubscription->ends_at->format('d/m/Y') }}
                    </span>
                </div>
                <form method="POST" action="{{ route('subscriptions.cancel') }}" class="mt-6">
                    @csrf
                    <button type="submit"
                            class="w-full py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium transition-all shadow-sm">
                        Cancelar Assinatura
                    </button>
                </form>
            </div>
        @endif

        <h3 class="text-lg font-bold text-gray-900 mb-6">Planos Disponíveis</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($plans as $plan)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100
                    {{ $currentSubscription && $currentSubscription->plan_id === $plan->id ? 'ring-2 ring-green-500 bg-green-50' : '' }}
                    hover:shadow-md transition-all group">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $plan->name }}</h3>
                                @if ($plan->is_featured)
                                    <span class="text-xs font-medium bg-amber-100 text-amber-700 px-2 py-1 rounded">
                                        Mais Popular
                                    </span>
                                @endif
                                @if ($currentSubscription && $currentSubscription->plan_id === $plan->id)
                                    <span class="text-xs font-medium bg-green-100 text-green-700 px-2 py-1 rounded">
                                        Atual
                                    </span>
                                @endif
                            </div>
                        </div>

                        <p class="text-gray-600 text-sm mb-6 line-clamp-2">{{ $plan->description }}</p>

                        <div class="mb-6">
                            @if ($plan->price_monthly > 0)
                                <p class="text-3xl font-bold text-gray-900">R$ {{ number_format($plan->price_monthly, 2, ',', '.') }}<span class="text-lg font-normal text-gray-500">/mês</span></p>
                            @else
                                <p class="text-3xl font-bold text-green-600">Grátis</p>
                            @endif
                            <p class="text-xs text-gray-500">Taxa: {{ $plan->commission_percentage }}% (mín. R$ {{ number_format($plan->min_commission, 2, ',', '.') }})</p>
                        </div>

                        <div class="space-y-3 mb-6">
                            <div class="flex items-center gap-2 text-sm text-gray-700">
                                <i data-lucide="file-text" class="w-4 h-4 text-gray-400"></i>
                                <span>{{ $plan->proposals_per_month }} propostas/mês</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-700">
                                <i data-lucide="image" class="w-4 h-4 text-gray-400"></i>
                                <span>{{ $plan->max_portfolio_items }} itens portfólio</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-700">
                                <i data-lucide="clock" class="w-4 h-4 text-gray-400"></i>
                                <span>Saque em {{ $plan->withdrawal_days }} dias úteis</span>
                            </div>
                        </div>

                        @if (!$currentSubscription || $currentSubscription->plan_id !== $plan->id)
                            <form method="POST" action="{{ route('subscriptions.store', $plan) }}">
                                @csrf
                                <input type="hidden" name="payment_method" value="pix">
                                <button type="submit"
                                        class="w-full py-3.5
                                            {{ $plan->price_monthly > 0 ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700' }}
                                            text-white rounded-xl font-semibold transition-all shadow-lg transform active:scale-95">
                                    {{ $plan->price_monthly > 0 ? 'Assinar por R$ ' . number_format($plan->price_monthly, 2, ',', '.') : 'Escolher Plano Gratuito' }}
                                </button>
                            </form>
                        @else
                            <button disabled
                                    class="w-full py-3.5 bg-gray-200 text-gray-400 rounded-xl font-medium">
                                Plano Ativo
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</x-layout>
