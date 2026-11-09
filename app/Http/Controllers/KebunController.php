<?php

namespace App\Http\Controllers;

use App\Http\Requests\KebunRequest;
use App\Models\Kebun;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KebunController extends Controller
{
    public function index(Request $request): View
    {
        return view('kebun.index', [
            'kebuns' => $request->user()->kebuns()->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('kebun.create', [
            'kebun' => new Kebun,
        ]);
    }

    public function store(KebunRequest $request): RedirectResponse
    {
        $request->user()->kebuns()->create($request->validated());

        return redirect()->route('kebun.index')->with('success', 'Kebun berhasil ditambahkan.');
    }

    public function edit(Request $request, Kebun $kebun): View
    {
        return view('kebun.edit', [
            'kebun' => $request->user()->kebuns()->findOrFail($kebun->getKey()),
        ]);
    }

    public function update(KebunRequest $request, Kebun $kebun): RedirectResponse
    {
        $milikPengguna = $request->user()->kebuns()->findOrFail($kebun->getKey());
        $milikPengguna->update($request->validated());

        return redirect()->route('kebun.index')->with('success', 'Data kebun berhasil diperbarui.');
    }

    public function destroy(Request $request, Kebun $kebun): RedirectResponse
    {
        $milikPengguna = $request->user()->kebuns()->findOrFail($kebun->getKey());
        $milikPengguna->delete();

        return redirect()->route('kebun.index')->with('success', 'Kebun berhasil dihapus.');
    }
}
