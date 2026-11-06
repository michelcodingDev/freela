<x-layout title="Editar Projeto - FreelaJob">

    @php
        $selectedSkills = $project->skills->pluck('id')->toArray();
    @endphp

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Editar Projeto</h1>
                    <p class="text-gray-600 text-sm">Atualize as informações do seu projeto.</p>
                </div>
                <a href="{{ route('projects.show', $project) }}"
                   class="text-sm text-green-600 hover:text-green-700 font-medium">
                    ← Voltar ao projeto
                </a>
            </div>

            <form method="POST" action="{{ route('projects.update', $project) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Categoria *</label>
                        <select name="category_id" id="category_id" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                            <option value="">Selecione uma categoria</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ $project->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título do Projeto *</label>
                        <input type="text" name="title" id="title"
                               value="{{ old('title', $project->title) }}"
                               required
                               maxlength="255"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                        @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição Detalhada *</label>
                    <textarea name="description" id="description" rows="6" required
                              class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">{{ old('description', $project->description) }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Mínimo de 50 caracteres.</p>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="experience_level" class="block text-sm font-medium text-gray-700 mb-1">Nível de Experiência *</label>
                        <select name="experience_level" id="experience_level" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                            <option value="iniciante" {{ old('experience_level', $project->experience_level) === 'iniciante' ? 'selected' : '' }}>Iniciante</option>
                            <option value="intermediario" {{ old('experience_level', $project->experience_level) === 'intermediario' ? 'selected' : '' }}>Intermediário</option>
                            <option value="especialista" {{ old('experience_level', $project->experience_level) === 'especialista' ? 'selected' : '' }}>Especialista</option>
                        </select>
                    </div>

                    <div>
                        <label for="visibility" class="block text-sm font-medium text-gray-700 mb-1">Visibilidade *</label>
                        <select name="visibility" id="visibility" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                            <option value="publico" {{ old('visibility', $project->visibility) === 'publico' ? 'selected' : '' }}>Público</option>
                            <option value="privado" {{ old('visibility', $project->visibility) === 'privado' ? 'selected' : '' }}>Privado</option>
                        </select>
                    </div>

                    <div>
                        <label for="proposal_deadline" class="block text-sm font-medium text-gray-700 mb-1">Prazo para Propostas *</label>
                        <input type="date" name="proposal_deadline" id="proposal_deadline" required
                               value="{{ old('proposal_deadline', $project->proposal_deadline->format('Y-m-d')) }}"
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
                               value="{{ old('budget_min', $project->budget_min) }}"
                               step="0.01" min="0"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                    </div>

                    <div>
                        <label for="budget_max" class="block text-sm font-medium text-gray-700 mb-1">Orçamento Máximo (R$)</label>
                        <input type="number" name="budget_max" id="budget_max"
                               value="{{ old('budget_max', $project->budget_max) }}"
                               step="0.01" min="0"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                    </div>
                </div>

                <div>
                    <label for="skills" class="block text-sm font-medium text-gray-700 mb-1">Habilidades Necessárias</label>
                    <div class="flex flex-wrap gap-2" id="skills-container">
                        @foreach ($skills as $skill)
                            <label class="flex items-center gap-2 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition-all group
                                @if (in_array($skill->id, $selectedSkills)) bg-blue-50 border-blue-300 @endif">
                                <input type="checkbox" name="skills[]" value="{{ $skill->id }}"
                                       class="w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500 skill-checkbox group-hover:text-green-600
                                      @if (in_array($skill->id, $selectedSkills)) checked @endif">
                                <span class="text-sm text-gray-700 group-hover:text-green-700 transition-colors">
                                    {{ $skill->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Selecione as habilidades que o freelancer ideal deve ter.</p>
                </div>

                <div class="border-t border-gray-100 pt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Anexos Existentes</label>
                    @if ($project->attachments->isEmpty())
                        <p class="text-sm text-gray-500">Nenhum anexo cadastrado.</p>
                    @else
                        <div class="space-y-3 mb-4">
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
                    @endif

                    <label class="block text-sm font-medium text-gray-700 mb-3">Novos Anexos (Opcional)</label>
                    <p class="text-sm text-gray-500 mb-3">Adicione mais arquivos se necessário. Máximo 10MB por arquivo.</p>
                    <input type="file" name="attachments[]" id="attachments" multiple
                           class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                </div>

                <div class="pt-8 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                            class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition-all shadow-lg transform active:scale-95 flex items-center gap-2">
                        <i data-lucide="save" class="w-5 h-5"></i>
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layout>
