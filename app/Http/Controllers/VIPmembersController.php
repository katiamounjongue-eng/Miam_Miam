<?php

namespace App\Http\Controllers;

use App\Models\VIP_members;
use App\Models\Users;
use Illuminate\Http\Request;

class VIPmembersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vipMembers = VIP_members::with('user')->get();
        return view('vip.index', compact('vipMembers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = Users::all();
        return view('vip.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vip_id' => 'required|string|max:8|unique:vip_members',
            'user_id' => 'required|exists:users,user_id',
            'vip_starting_date' => 'required|date',
            'vip_ending_date' => 'required|date|after_or_equal:vip_starting_date',
        ]);

        VIP_members::create($request->all());
        return redirect()->route('vip.index')->with('success', 'Membre VIP ajouté.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $vipMember = VIP_members::with('user')->findOrFail($id);
        return view('vip.show', compact('vipMember'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $vipMember = VIP_members::findOrFail($id);
        $users = Users::all();
        return view('vip.edit', compact('vipMember', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $vipMember = VIP_members::findOrFail($id);
        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'vip_starting_date' => 'required|date',
            'vip_ending_date' => 'required|date|after_or_equal:vip_starting_date',
        ]);

        $vipMember->update($request->all());
        return redirect()->route('vip.index')->with('success', 'Membre VIP mis à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $vipMember = VIP_members::findOrFail($id);
        $vipMember->delete();
        return redirect()->route('vip.index')->with('success', 'Membre VIP supprimé.');
    }
}
