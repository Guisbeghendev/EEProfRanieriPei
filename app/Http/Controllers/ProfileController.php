<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Profile;
use App\Models\Avatar; // Importa o modelo Avatar
use Exception;

class ProfileController extends Controller
{
    /**
     * Exibe o perfil do usuário logado, carregando também o profile e seu avatar.
     */
    public function show(Request $request): Response
    {
        return Inertia::render('Profile/Show', [
            'user' => $request->user()->load('profile.avatarRelation'),
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'user' => $request->user()->load('profile.avatarRelation'),
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Atualiza os campos do modelo User
        $user->fill($request->validated());
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        // Obtém ou cria o perfil do usuário.
        // 'firstOrCreate' já cuida da criação e salvamento se o perfil não existir.
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);

        // Recarrega o perfil com a relação do avatar para garantir que o estado mais recente está disponível
        $profile->loadMissing('avatarRelation');

        // Pega todos os dados validados da requisição.
        $profileData = $request->validated();

        // Remove os campos de usuário (name, email) para evitar preenchimento indesejado no profile
        unset($profileData['name']);
        unset($profileData['email']);

        // --- Lógica para o Avatar com Tabela Separada ---
        if ($request->hasFile('avatar')) {
            $uploadedAvatar = $request->file('avatar');
            $storageDir = 'avatars'; // Diretório dentro de storage/app/public

            // Gera o nome do arquivo: profile_id.extensao (MELHORIA DE CONSISTÊNCIA)
            $filename = $profile->id . '.' . $uploadedAvatar->getClientOriginalExtension();
            $fullStoragePath = $storageDir . '/' . $filename; // Caminho dentro de storage/app/public
            $publicUrlPath = Storage::url($fullStoragePath); // Caminho público para o navegador

            // Se já existe um avatar associado, exclui o arquivo e o registro do banco de dados
            if ($profile->avatarRelation) {
                if (Storage::disk('public')->exists($profile->avatarRelation->path)) {
                    Storage::disk('public')->delete($profile->avatarRelation->path);
                    \Log::info("ProfileController: Arquivo de avatar antigo excluído: {$profile->avatarRelation->path}");
                }
                $profile->avatarRelation->delete(); // Exclui o registro do Avatar no banco de dados
                \Log::info("ProfileController: Registro de avatar antigo excluído para profile_id: {$profile->id}");
            }

            try {
                // Salva o novo arquivo no disco 'public'
                $pathSaved = $uploadedAvatar->storeAs($storageDir, $filename, 'public');

                // Cria um novo registro na tabela 'avatars' e associa ao perfil
                $profile->avatarRelation()->create([
                    'path' => $pathSaved,
                    'url' => $publicUrlPath,
                    'original_filename' => $uploadedAvatar->getClientOriginalName(),
                    'mime_type' => $uploadedAvatar->getMimeType(),
                    'size' => $uploadedAvatar->getSize(),
                ]);
                \Log::info("ProfileController: Novo avatar criado para profile_id: {$profile->id} em {$fullStoragePath}");

            } catch (Exception $e) {
                \Log::error("ProfileController: Falha ao salvar avatar para usuário {$user->id}: " . $e->getMessage() . " na linha " . $e->getLine() . " em " . $e->getFile());
                // Se houve erro, garante que não há avatar linkado
                if ($profile->avatarRelation()->exists()) {
                    $profile->avatarRelation()->delete();
                }
            }

        } elseif (array_key_exists('remove_avatar', $profileData) && $profileData['remove_avatar']) {
            // Se o usuário marcou para remover o avatar
            if ($profile->avatarRelation) {
                if (Storage::disk('public')->exists($profile->avatarRelation->path)) {
                    Storage::disk('public')->delete($profile->avatarRelation->path);
                    \Log::info("ProfileController: Avatar removido do disco: {$profile->avatarRelation->path}");
                }
                $profile->avatarRelation->delete(); // Exclui o registro do Avatar do banco de dados
                \Log::info("ProfileController: Registro de avatar removido para profile_id: {$profile->id}");
            }
        }

        // Atualiza os outros campos do perfil.
        // Usamos 'only' para garantir que apenas os campos do perfil sejam preenchidos,
        // ignorando 'avatar' e 'remove_avatar' que já foram tratados.
        $profile->fill($request->only([
            'birth_date',
            'address',
            'city',
            'state',
            'whatsapp',
            'other_contact',
            'ranieri_text',
            'biography',
        ]));
        $profile->save(); // Salva o perfil no banco de dados

        // CORREÇÃO: Adicionado ->withStatus(303) para que o Inertia.js lide corretamente com o redirecionamento após PATCH.
        return Redirect::route('profile.edit')->with('status', 'profile-updated')->withStatus(303);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // CORREÇÃO: Adicionado ->withStatus(303) para que o Inertia.js lide corretamente com o redirecionamento após DELETE.
        return Redirect::to('/')->withStatus(303);
    }
}
