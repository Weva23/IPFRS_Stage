<?php

namespace App\Http\Livewire\ExampleLaravel;

use Illuminate\Http\Request;
use App\Models\Professeur;
use App\Models\Typeymntprofs;
use App\Models\Country;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProfesseurExport;

class ProfesseurController extends Component
{
    public function liste_prof()
    {
        $profs = Professeur::paginate(4);
        $countries = Country::all();
        $typeymntprofs = Typeymntprofs::all();
        return view('livewire.example-laravel.prof-management', compact('profs', 'countries', 'typeymntprofs'));
    }

    public function store(Request $request)
{
    $request->validate([
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'nomprenom' => 'required|string',
        'diplome' => 'nullable|string',
        'genre' => 'required|string',
        'lieunaissance' => 'nullable|string',
        'adress' => 'nullable|string',
        'datenaissance' => 'nullable|date',
        'email' => 'nullable|email',
        'phone' => 'required|integer',
        'wtsp' => 'nullable|integer',
        'country_id' => 'required|exists:countries,id',
        'type_id' => 'required|exists:typeymntprofs,id',
    ]);

    try {
        $imageName = $request->hasFile('image') ? time() . '.' . $request->image->extension() : null;

        if ($imageName) {
            $request->image->move(public_path('images'), $imageName);
        }

        $prof = Professeur::create([
            'image' => $imageName,
            'nomprenom' => $request->nomprenom,
            'diplome' => $request->diplome,
            'genre' => $request->genre,
            'lieunaissance' => $request->lieunaissance,
            'adress' => $request->adress,
            'datenaissance' => $request->datenaissance,
            'email' => $request->email,
            'phone' => $request->phone,
            'wtsp' => $request->wtsp,
            'country_id' => $request->country_id,
            'type_id' => $request->type_id,
        ]);

        return response()->json(['success' => 'Professeur créé avec succès', 'prof' => $prof->load('country', 'type')]);
    } catch (\Throwable $th) {
        return response()->json(['error' => $th->getMessage()], 500);
    }
}

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'nomprenom' => 'required|string',
        'diplome' => 'nullable|string',
        'genre' => 'required|string',
        'lieunaissance' => 'nullable|string',
        'adress' => 'nullable|string',
        'datenaissance' => 'nullable|date',
        'email' => 'nullable|email',
        'phone' => 'required|integer',
        'wtsp' => 'nullable|integer',
        'country_id' => 'required|exists:countries,id',
        'type_id' => 'required|exists:typeymntprofs,id',
    ]);

    try {
        $prof = Professeur::findOrFail($id);

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('images'), $imageName);
            $validated['image'] = $imageName;
        }

        $prof->update($validated);

        return response()->json(['success' => 'Professeur modifié avec succès', 'prof' => $prof->load('country', 'type')]);
    } catch (\Throwable $th) {
        return response()->json(['error' => $th->getMessage()], 500);
    }
}


    public function delete_prof($id)
    {
        $prof = Professeur::findOrFail($id);
        $prof->delete();

        return response()->json(['success' => 'Professeur supprimé avec succès']);
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $profs = Professeur::where(function($query) use ($search) {
            $query->where('id', 'like', "%$search%")
                ->orWhere('nomprenom', 'like', "%$search%")
                ->orWhere('diplome', 'like', "%$search%")
                ->orWhere('genre', 'like', "%$search%")
                ->orWhere('lieunaissance', 'like', "%$search%")
                ->orWhere('adress', 'like', "%$search%")
                ->orWhere('datenaissance', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%")
                ->orWhere('wtsp', 'like', "%$search%");
        })->paginate(4);

        $countries = Country::all();
        $typeymntprofs = Typeymntprofs::all();
        return view('livewire.example-laravel.prof-management', compact('profs', 'countries', 'search', 'typeymntprofs'));
    }

    public function export()
    {
        return Excel::download(new ProfesseurExport, 'Professeurs.xlsx');
    }

    public function render()
    {
        $profs = Professeur::paginate(4);
        $countries = Country::all();
        $typeymntprofs = Typeymntprofs::all();
        return view('livewire.example-laravel.prof-management', compact('profs', 'countries', 'typeymntprofs'));
    }
}
