<x-layout title="Política de Privacidade">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('home') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">← Voltar ao início</a>

        <h1 class="text-3xl font-bold text-gray-900 mt-3 mb-2">Política de Privacidade</h1>
        <p class="text-sm text-gray-500 mb-8">Última atualização: {{ now()->format('d/m/Y') }}</p>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-6 text-gray-700 leading-relaxed">
            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">1. Dados coletados</h2>
                <p>Coletamos os dados informados no cadastro (nome, e-mail e tipo de conta), dados de perfil profissional, informações de projetos, propostas, mensagens e registros financeiros necessários para operar a custódia e os saques.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">2. Como usamos os dados</h2>
                <p>Os dados são utilizados para autenticar o acesso, viabilizar a conexão entre clientes e freelancers, processar pagamentos, prevenir fraudes, enviar notificações sobre a sua conta e melhorar a experiência na plataforma.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">3. Compartilhamento</h2>
                <p>Compartilhamos apenas as informações necessárias entre cliente e freelancer para a execução do projeto. Dados financeiros podem ser processados por gateways de pagamento parceiros. Não vendemos dados pessoais a terceiros.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">4. Segurança</h2>
                <p>Adotamos medidas técnicas e organizacionais para proteger os dados, incluindo controle de acesso por perfil, registro de auditoria das ações administrativas e proteção de anexos por autorização.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">5. Retenção</h2>
                <p>Mantemos os dados enquanto a conta estiver ativa e pelo período necessário para cumprir obrigações legais, fiscais e de prevenção a fraudes.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">6. Seus direitos</h2>
                <p>Você pode solicitar acesso, correção ou exclusão dos seus dados, nos termos da Lei Geral de Proteção de Dados (LGPD). Para exercer esses direitos, entre em contato com o suporte.</p>
            </section>

            <p class="text-sm text-gray-500 border-t border-gray-100 pt-6">Precisa de ajuda com seus dados? <a href="{{ route('pages.support') }}" class="text-green-600 hover:underline font-medium">Fale com o suporte</a>.</p>
        </div>
    </div>
</x-layout>
