<?php

namespace App\Http\Livewire\ExampleLaravel;

use Illuminate\Http\Request;
use App\Models\ContenusFormation;
use App\Models\Formations;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ContenusFormationExport;

class ContenusFormationController extends Component
{
    public function liste_contenue()
    {
        $contenues = ContenusFormation::with('formation')
            ->join('formations', 'contenus_formations.formation_id', '=', 'formations.id')
            ->orderBy('formations.nom')
            ->select('contenus_formations.*')
            ->paginate(4);

        $formations = Formations::all();
        return view('livewire.example-laravel.contenusformation-management', compact('contenues', 'formations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomchap' => 'required|string',
            'nomunite' => 'required|string',
            'description' => 'nullable|string',
            'nombreheures' => 'required|integer',
            'formation_id' => 'required|exists:formations,id',
        ]);

        try {
            ContenusFormation::create([
                'nomchap' => $request->nomchap,
                'nomunite' => $request->nomunite,
                'description' => $request->description,
                'nombreheures' => $request->nombreheures,
                'formation_id' => $request->formation_id,
            ]);

            return response()->json(['success' => 'Contenu créé avec succès']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nomchap' => 'required|string',
            'nomunite' => 'required|string',
            'description' => 'nullable|string',
            'nombreheures' => 'required|integer',
            'formation_id' => 'required|exists:formations,id',
        ]);

        try {
            $contenue = ContenusFormation::findOrFail($id);
            $contenue->update($validated);

            return response()->json(['success' => 'Contenu modifié avec succès']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function delete_contenue($id)
    {
        try {
            $contenue = ContenusFormation::findOrFail($id);
            $contenue->delete();

            return response()->json(['success' => 'Contenu supprimé avec succès']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function search3(Request $request)
    {
        if ($request->ajax()) {
            $search3 = $request->search3;
            $contenues = ContenusFormation::with('formation')
                ->where('nomchap', 'like', "%$search3%")
                ->orWhere('nomunite', 'like', "%$search3%")
                ->orWhere('description', 'like', "%$search3%")
                ->orWhere('nombreheures', 'like', "%$search3%")
                ->paginate(4);

            $view = view('livewire.example-laravel.contenusformation-management', compact('contenues'))->render();
            return response()->json(['html' => $view]);
        }
    }

    public function render()
    {
        return $this->liste_contenue();
    }

    public function export()
    {
        return Excel::download(new ContenusFormationExport(), 'ContenusFormation.xlsx');
    }

    public function show($id)
    {
        $formation = Formations::with('contenusFormation')->find($id);

        if ($formation) {
            return response()->json(['formation' => $formation, 'contenus' => $formation->contenusFormation]);
        } else {
            return response()->json(['error' => 'Formation non trouvée'], 404);
        }
    }
}

