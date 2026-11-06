<x-layout title="Criar Projeto - FreelaJob">

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Criar Novo Projeto</h1>
                    <p class="text-gray-600 text-sm">Descreva o que você precisa e receba propostas de freelancers.</p>
                </div>
                <a href="{{ route('projects.index') }}"
                   class="text-sm text-green-600 hover:text-green-700 font-medium">
                    ← Voltar aos projetos
                </a>
            </div>

            <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Categoria *</label>
                        <select name="category_id" id="category_id" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                            <option value="">Selecione uma categoria</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título do Projeto *</label>
                        <input type="text" name="title" id="title"
                               value="{{ old('title') }}"
                               required
                               maxlength="255"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                               placeholder="Ex: Desenvolvimento de Loja Virtual">
                        @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição Detalhada *</label>
                    <textarea name="description" id="description" rows="6" required
                              class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                              placeholder="Descreva detalhadamente o que você precisa, entregáveis esperados, tecnologias envolvidas, prazos e qualquer outra informação importante para os freelancers.">{{ old('description') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Mínimo de 50 caracteres. Não inclua dados de contato ou valores - os freelancers definirão seus próprios preços nas propostas.</p>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="experience_level" class="block text-sm font-medium text-gray-700 mb-1">Nível de Experiência *</label>
                        <select name="experience_level" id="experience_level" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                            <option value="iniciante">Iniciante - Menores valores</option>
                            <option value="intermediario">Intermediário - Experiência e valor equilibrados</option>
                            <option value="especialista">Especialista - Valores mais elevados</option>
                        </select>
                    </div>

                    <div>
                        <label for="visibility" class="block text-sm font-medium text-gray-700 mb-1">Visibilidade *</label>
                        <select name="visibility" id="visibility" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                            <option value="publico">Público - Qualquer freelancer pode propor</option>
                            <option value="privado">Privado - Apenas freelancers convidados</option>
                        </select>
                    </div>

                    <div>
                        <label for="proposal_deadline" class="block text-sm font-medium text-gray-700 mb-1">Prazo para Propostas *</label>
                        <input type="date" name="proposal_deadline" id="proposal_deadline" required
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                        @error('proposal_deadline')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="budget_min" class="block text-sm font-medium text-gray-700 mb-1">Orçamento Mínimo (R$)</label>
                        <input type="number" name="budget_min" id="budget_min"
                               value="{{ old('budget_min') }}"
                               step="0.01" min="0"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                               placeholder="0,00">
                    </div>

                    <div>
                        <label for="budget_max" class="block text-sm font-medium text-gray-700 mb-1">Orçamento Máximo (R$)</label>
                        <input type="number" name="budget_max" id="budget_max"
                               value="{{ old('budget_max') }}"
                               step="0.01" min="0"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                               placeholder="0,00">
                        <p class="text-xs text-gray-500 mt-1">Opcional. Deixe em branco para não definir limite.</p>
                    </div>
                </div>

                <div>
                    <label for="skills" class="block text-sm font-medium text-gray-700 mb-1">Habilidades Necessárias</label>
                    <div class="flex flex-wrap gap-2" id="skills-container">
                        @foreach ($skills as $skill)
                            <label class="flex items-center gap-2 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition-all group">
                                <input type="checkbox" name="skills[]" value="{{ $skill->id }}"
                                       class="w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500 skill-checkbox group-hover:text-green-600">
                                <span class="text-sm text-gray-700 group-hover:text-green-700 transition-colors">
                                    {{ $skill->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Selecione as habilidades que o freelancer ideal deve ter.</p>
                    @error('skills')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-gray-100 pt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Anexos (Opcional)</label>
                    <p class="text-sm text-gray-500 mb-3">Anexe arquivos que complementem a descrição do projeto (briefing, referências, etc). Máximo 10MB por arquivo.</p>
                    <input type="file" name="attachments[]" id="attachments" multiple
                           class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                </div>

                <div class="pt-8 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                            class="px-8 py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition-all shadow-lg transform active:scale-95 flex items-center gap-2">
                        <i data-lucide="briefcase" class="w-5 h-5"></i>
                        Publicar Projeto
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layout>
