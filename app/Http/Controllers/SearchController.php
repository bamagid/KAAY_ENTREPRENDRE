<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $searchTerm = $request->query('q');

        // Recherche des ressources par titre
        $ressources = DB::table('ressources')
            ->select('id', 'titre', 'description')
            ->where('is_deleted', false)
            ->where('titre', 'LIKE', "%$searchTerm%")
            ->get();

        // Recherche des secteurs par nom du secteur
        $secteurs = DB::table('secteurs')
            ->select('id', 'nomSecteur')
            ->where('is_deleted', false)
            ->where('nomSecteur', 'LIKE', "%$searchTerm%")
            ->get();

        // Recherche des événements par nom d'événement
        $evenements = DB::table('evenements')
            ->select('id', 'nomEvenement', 'type', 'dateEvenement', 'lieuEvenement', 'description', 'image')
            ->where('is_deleted', false)
            ->where('nomEvenement', 'LIKE', "%$searchTerm%")
            ->get();

        return response()->json([
            'ressources' => $ressources,
            'secteurs' => $secteurs,
            'evenements' => $evenements,
        ]);
    }
}
