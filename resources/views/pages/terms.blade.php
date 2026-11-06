<x-layout title="Termos de Uso">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('home') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">← Voltar ao início</a>

        <h1 class="text-3xl font-bold text-gray-900 mt-3 mb-2">Termos de Uso</h1>
        <p class="text-sm text-gray-500 mb-8">Última atualização: {{ now()->format('d/m/Y') }}</p>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-6 text-gray-700 leading-relaxed">
            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">1. Sobre a plataforma</h2>
                <p>A FreelaJob é um marketplace que conecta clientes e freelancers. A plataforma disponibiliza ferramentas de publicação de projetos, envio de propostas, comunicação, avaliação e pagamento em custódia (escrow).</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">2. Cadastro e conta</h2>
                <p>Para utilizar os recursos da plataforma é necessário criar uma conta com informações verdadeiras e mantê-las atualizadas. O usuário é responsável pela confidencialidade de suas credenciais e por todas as atividades realizadas em sua conta.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">3. Projetos, propostas e contratação</h2>
                <p>Ao publicar um projeto, o cliente declara que as informações são corretas e que possui recursos para contratar o serviço. Ao enviar uma proposta, o freelancer compromete-se a executar o escopo combinado nos prazos informados. O aceite de uma proposta cria um vínculo direto entre cliente e freelancer.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">4. Pagamentos e custódia</h2>
                <p>Os pagamentos são realizados por meio dos métodos disponibilizados na plataforma. O valor fica retido em custódia até a entrega do projeto ser aprovada pelo cliente ou até a liberação automática após o prazo previsto. A plataforma retém uma taxa de serviço informada no momento do pagamento.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">5. Saques</h2>
                <p>O freelancer pode solicitar o saque do saldo disponível via Pix. O valor mínimo, a taxa e o prazo de processamento são informados na tela de saques. Saques pendentes podem ser cancelados pelo freelancer ou pelo administrador em caso de inconsistência.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">6. Disputas</h2>
                <p>Caso cliente e freelancer não cheguem a um acordo sobre a entrega, qualquer uma das partes pode abrir uma disputa. A equipe da FreelaJob analisará as informações e poderá liberar o pagamento ao freelancer ou reembolsar o cliente.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">7. Condutas proibidas</h2>
                <p>É proibido publicar conteúdo ilegal, ofensivo ou enganoso, tentar burlar o sistema de pagamentos, assediar outros usuários ou utilizar a plataforma para atividades fraudulentas. Contas que violarem estas regras podem ser suspensas ou banidas.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">8. Alterações</h2>
                <p>Estes termos podem ser atualizados a qualquer momento. O uso contínuo da plataforma após as alterações representa a concordância com a versão vigente.</p>
            </section>

            <p class="text-sm text-gray-500 border-t border-gray-100 pt-6">Dúvidas sobre estes termos? <a href="{{ route('pages.support') }}" class="text-green-600 hover:underline font-medium">Fale com o suporte</a>.</p>
        </div>
    </div>
</x-layout>
