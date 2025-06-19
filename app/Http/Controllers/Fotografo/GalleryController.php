<?php

namespace App\Http\Controllers\Fotografo;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Group;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Jobs\ProcessImageWithGd; // Usando barra invertida para namespace
use App\Models\Image;

class GalleryController extends Controller
{
    // REMOVIDO: O middleware 'auth' já é aplicado no nível da rota em web.php
    /* public function __construct()
    {
        $this->middleware('auth');
    } */

    /**
     * Exibe a lista de galerias com filtros.
     */
    public function index(Request $request)
    {
        // Pega os filtros da requisição
        $filters = $request->only(['group_id', 'event_date']);

        // Inicia a query para as galerias do fotógrafo logado
        // Eager load 'groups' para evitar N+1 query problem na view
        $galleriesQuery = Gallery::where('user_id', Auth::id())
            ->with('groups');

        // Aplica o filtro por group_id, se presente
        if (!empty($filters['group_id'])) {
            $galleriesQuery->whereHas('groups', function ($query) use ($filters) {
                $query->where('groups.id', $filters['group_id']);
            });
        }

        // Aplica o filtro por event_date, se presente
        if (!empty($filters['event_date'])) {
            // whereDate compara apenas a data, ignorando a parte da hora
            $galleriesQuery->whereDate('event_date', $filters['event_date']);
        }

        // Busca as galerias e as ordena pela data do evento, das mais recentes para as mais antigas
        $galleries = $galleriesQuery->orderBy('event_date', 'desc')->get();

        // Pega todos os grupos para popular o campo de filtro na view
        $groups = Group::orderBy('name')->get(['id', 'name']);

        // Renderiza a view Inertia, passando os dados necessários
        return Inertia::render('Fotografo/Galleries/ListGalleries', [
            'galleries' => $galleries,
            'groups' => $groups,
            'filters' => $filters, // Importante para preencher os campos de filtro na view
        ]);
    }

    /**
     * Exibe o formulário para criar uma nova galeria.
     */
    public function create()
    {
        Gate::authorize('create', Gallery::class);
        $groups = Group::orderBy('name')->get(['id', 'name']);
        return Inertia::render('Fotografo/Galleries/Create', [
            'groups' => $groups,
        ]);
    }

    /**
     * Armazena uma nova galeria no banco de dados.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Gallery::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_date' => ['nullable', 'date'],
            'selected_group_ids' => ['required', 'array'],
            'selected_group_ids.*' => ['integer', 'exists:groups,id'],
            'selected_watermark' => ['nullable', 'string', 'max:255'],
        ]);

        $gallery = new Gallery();
        $gallery->user_id = Auth::id();
        $gallery->title = $validated['title'];
        $gallery->description = $validated['description'];
        $gallery->event_date = $validated['event_date'];
        $gallery->watermark_file_used = $validated['selected_watermark'];
        $gallery->save();

        $gallery->groups()->attach($validated['selected_group_ids']);

        // Respostas JSON para Inertia geralmente não precisam de ->withStatus(303)
        // O Inertia.js lida com respostas JSON automaticamente.
        return response()->json([
            'success_message' => 'Galeria "' . $gallery->title . '" criada com sucesso!',
            'gallery_id' => $gallery->id
        ], 201);
    }

    /**
     * Exibe a página de upload de imagens para uma galeria específica.
     * Esta é a view para o novo componente UploadImg.vue.
     */
    public function uploadImages(Gallery $gallery)
    {
        Gate::authorize('manage-gallery', $gallery);
        return Inertia::render('Fotografo/Galleries/UploadImg', [
            'gallery' => $gallery,
        ]);
    }

    /**
     * Lida com o upload de imagens individuais para uma galeria específica.
     *
     * @param Request $request
     * @param Gallery $gallery
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeImage(Request $request, Gallery $gallery)
    {
        Gate::authorize('manage-gallery', $gallery);

        $request->validate([
            'file' => ['required', 'image', 'max:20480'], // Valida o arquivo recebido (20MB)
        ]);

        $uploadedFile = $request->file('file');
        $originalFileName = $uploadedFile->getClientOriginalName();

        // 1. Gera um nome de arquivo único para o arquivo temporário
        $uniqueFilename = Str::uuid() . '_' . Str::slug(pathinfo($originalFileName, PATHINFO_FILENAME)) . '.' . $uploadedFile->getClientOriginalExtension();

        // 2. Define um diretório temporário interno (não público) dentro de storage/app
        $tempDir = 'uploads/temp_images';
        Storage::disk('local')->makeDirectory($tempDir); // Garante que o diretório exista

        // 3. Armazena o arquivo temporariamente no disco 'local' (storage/app/uploads/temp_images)
        // Isso é mais seguro para jobs da fila, pois não está em um local público que possa ser limpo
        $tempRelativePath = Storage::disk('local')->putFileAs($tempDir, $uploadedFile, $uniqueFilename);

        // O nome do arquivo da marca d'água é lido do cabeçalho HTTP
        $watermarkFile = $request->header('X-Watermark-File');

        // IMPORTANTE: Agora, dispara o job ProcessImageWithGd
        // Passa o CAMINHO RELATIVO do arquivo temporário para o job
        ProcessImageWithGd::dispatch($tempRelativePath, (int) $gallery->id, $originalFileName, $watermarkFile);

        // Retornar JSON (como já está) é a forma correta para o Inertia.js para uploads assíncronos.
        return response()->json(['success' => true, 'message' => 'Imagem enfileirada para processamento!']);
    }


    /**
     * Exibe o formulário para editar uma galeria existente.
     */
    public function edit(Gallery $gallery)
    {
        Gate::authorize('update', $gallery);
        $gallery->load('groups');
        $groups = Group::orderBy('name')->get(['id', 'name']);
        return Inertia::render('Fotografo/Galleries/Edit', [
            'gallery' => $gallery,
            'groups' => $groups,
            'selectedGroupIds' => $gallery->groups->pluck('id')->toArray(),
        ]);
    }

    /**
     * Atualiza uma galeria existente no banco de dados.
     */
    public function update(Request $request, Gallery $gallery)
    {
        Gate::authorize('update', $gallery);
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_date' => ['nullable', 'date'],
            'selected_group_ids' => ['required', 'array'],
            'selected_group_ids.*' => ['integer', 'exists:groups,id'],
        ]);

        $gallery->title = $validated['title'];
        $gallery->description = $validated['description'];
        $gallery->event_date = $validated['event_date'];
        $gallery->save();
        $gallery->groups()->sync($validated['selected_group_ids']);

        // Adicionado ->withStatus(303) para que o Inertia.js lide corretamente com o redirecionamento após PUT.
        return redirect()->route('fotografo.dashboard')
            ->with('success', 'Galeria atualizada com sucesso!')->withStatus(303);
    }

    /**
     * Exibe os detalhes de uma galeria e suas imagens.
     */
    public function show(Gallery $gallery)
    {
        // Garante que o fotógrafo logado é o dono da galeria
        if ($gallery->user_id !== Auth::id()) {
            abort(403); // Ação não autorizada
        }

        // Carrega as imagens relacionadas à galeria
        $gallery->load('images');

        return Inertia::render('Fotografo/Galleries/PreviewImages', [
            'gallery' => $gallery,
        ]);
    }

    /**
     * Remove uma galeria do banco de dados.
     */
    public function destroy(Gallery $gallery)
    {
        Gate::authorize('delete', $gallery);

        // TODO: Adicionar lógica para deletar imagens e pastas fisicamente
        // Isso pode ser feito aqui ou em um Observer para a Model Gallery.
        // Se onDelete('cascade') estiver na migração de 'images', os registros do DB serão deletados.
        // Para arquivos físicos, você precisaria iterar sobre gallery->images e deletar de Storage::disk('public')
        // Exemplo:
        // foreach ($gallery->images as $image) {
        //     Storage::disk('public')->delete([
        //         $image->path_original,
        //         $image->path_thumb,
        //         $image->metadata['watermarked_path'] ?? null
        //     ]);
        // }
        // Storage::disk('public')->deleteDirectory('galleries/' . $gallery->id); // Deleta a pasta da galeria

        $gallery->delete();
        // Adicionado ->withStatus(303) para que o Inertia.js lide corretamente com o redirecionamento após DELETE.
        return redirect()->route('fotografo.galleries.index')
            ->with('success', 'Galeria excluída com sucesso.')->withStatus(303);
    }

    /**
     * Remove uma imagem específica de uma galeria.
     *
     * @param Gallery $gallery
     * @param Image $image
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyImage(Gallery $gallery, Image $image)
    {
        // Autoriza que o usuário pode gerenciar a galeria à qual a imagem pertence.
        // Isso implica que ele pode deletar as imagens dela.
        Gate::authorize('manage-gallery', $gallery);

        // Garante que a imagem pertence à galeria correta
        if ($image->gallery_id !== $gallery->id) {
            abort(404); // Imagem não encontrada na galeria especificada
        }

        // Deleta os arquivos físicos da imagem
        $disk = Storage::disk('public');
        $filesToDelete = [];

        if ($image->path_original) {
            $filesToDelete[] = $image->path_original;
        }
        if ($image->path_thumb) {
            $filesToDelete[] = $image->path_thumb;
        }
        if (isset($image->metadata['watermarked_path']) && $image->metadata['watermarked_path']) {
            $filesToDelete[] = $image->metadata['watermarked_path'];
        }

        if (!empty($filesToDelete)) {
            $disk->delete($filesToDelete);
        }

        // Deleta o registro da imagem no banco de dados
        $image->delete();

        // Adicionado ->withStatus(303) para que o Inertia.js lide corretamente com o redirecionamento após DELETE.
        return redirect()->back()
            ->with('success', 'Imagem excluída com sucesso!')->withStatus(303);
    }


    /**
     * Retorna a lista de marcas d'água disponíveis.
     */
    public function getAvailableWatermarks(Request $request)
    {
        $watermarks = collect(Storage::disk('public')->files('watermarks'))
            ->map(function ($file) {
                return ['name' => basename($file), 'path' => Storage::url($file)];
            })->toArray();

        return response()->json($watermarks);
    }
}
