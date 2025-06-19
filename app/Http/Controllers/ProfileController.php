<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage; // Importar Storage para lidar com arquivos

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show(Request $request): Response
    {
        // Carrega o usuário autenticado com os relacionamentos 'profile' e 'avatar'.
        $user = $request->user()->load(['profile', 'avatar']);

        return Inertia::render('Profile/Show', [
            'user' => $user,
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        // Garante que o perfil e o avatar sejam carregados para a página de edição também.
        $user = $request->user()->load(['profile', 'avatar']);

        return Inertia::render('Profile/Edit', [
            'user' => $user,
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // 1. Atualiza os dados do User (nome e e-mail)
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        // 2. Atualiza ou cria o Profile do usuário
        // Pega todos os campos que pertencem ao modelo Profile
        $profileData = $request->only([
            'birth_date',
            'address',
            'city',
            'state',
            'whatsapp',
            'other_contact',
            'biography',
            'ranieri_text',
        ]);

        // Se o usuário já tem um perfil, atualiza. Senão, cria um novo.
        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            // Cria um novo perfil e associa ao usuário
            $user->profile()->create($profileData);
        }

        // 3. Lida com o upload ou remoção do Avatar
        if ($request->hasFile('avatar')) {
            // Remove o avatar antigo se existir
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar->path); // Assume 'path' é o nome da coluna no seu modelo Avatar que guarda o caminho
                $user->avatar->delete(); // Deleta o registro do avatar no banco de dados
            }

            // Salva o novo avatar
            $path = $request->file('avatar')->store('avatars', 'public'); // Salva em storage/app/public/avatars
            $user->avatar()->create([
                'url' => Storage::url($path), // Gera a URL pública para acesso
                'path' => $path, // Guarda o caminho interno para futura remoção
            ]);
        } elseif ($request->boolean('remove_avatar') && $user->avatar) {
            // Se a flag 'remove_avatar' for verdadeira e houver um avatar existente
            Storage::disk('public')->delete($user->avatar->path);
            $user->avatar->delete();
        }

        // Redireciona de volta para a página de perfil.
        return Redirect::to('/profile')->with('status', 'profile-updated');
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

        // Antes de deletar o usuário, delete o avatar e o perfil
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar->path);
            $user->avatar->delete();
        }
        if ($user->profile) {
            $user->profile->delete();
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
