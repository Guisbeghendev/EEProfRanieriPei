<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse; // Importado novamente para clareza

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $groups = Group::all();

        return Inertia::render('Admin/Groups/Index', [
            'groups' => $groups,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Groups/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:groups,name',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'O nome do grupo é obrigatório.',
            'name.unique' => 'Um grupo com este nome já existe.',
            'name.max' => 'O nome do grupo não pode ter mais de :max caracteres.',
            'description.max' => 'A descrição não pode ter mais de :max caracteres.',
        ]);

        Group::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // CORREÇÃO: Adicionado ->withStatus(303) para que o Inertia.js lide corretamente com o redirecionamento após POST.
        return redirect()->route('admin.groups.index')->with('success', 'Grupo criado com sucesso!')->withStatus(303);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        // Este método não será usado.
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group): Response
    {
        return Inertia::render('Admin/Groups/Edit', [
            'group' => $group,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group): RedirectResponse // Adicionado tipo de retorno
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:groups,name,' . $group->id, // unique:table,column,except_id
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'O nome do grupo é obrigatório.',
            'name.unique' => 'Um grupo com este nome já existe.',
            'name.max' => 'O nome do grupo não pode ter mais de :max caracteres.',
            'description.max' => 'A descrição não pode ter mais de :max caracteres.',
        ]);

        $group->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // CORREÇÃO: Adicionado ->withStatus(303) para que o Inertia.js lide corretamente com o redirecionamento após PUT/PATCH.
        return redirect()->route('admin.groups.index')->with('success', 'Grupo atualizado com sucesso!')->withStatus(303);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group): RedirectResponse // Adicionado tipo de retorno
    {
        $group->delete();

        // CORREÇÃO: Adicionado ->withStatus(303) para que o Inertia.js lide corretamente com o redirecionamento após DELETE.
        return redirect()->route('admin.groups.index')->with('success', 'Grupo excluído com sucesso!')->withStatus(303);
    }
}
