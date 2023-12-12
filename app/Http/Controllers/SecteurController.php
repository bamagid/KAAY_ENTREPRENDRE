<?php

namespace App\Http\Controllers;

use App\Models\Secteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Secteurs",
 *     description="Endpoints pour la gestion des secteurs."
 * )
 */
class SecteurController extends Controller
{

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @OA\Post(
     *      path="/api/secteur/create",
     *      operationId="createsecteur",
     *      tags={"Secteurs"},
     *      summary="Ajouter un nouveau secteur",
     *      description="Ajoute un nouveau secteur avec les détails fournis",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="nomSecteur", type="string"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Secteur créé avec succès",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Non autorisé",
     *      ),
     * )
     */
    public function store(Request $request)
    {
        if (!auth()->check() || auth()->user()->role_id !== 1) {
            return response()->json(['message' => 'Non autorisé'], 401);
        }

        $validator=Validator::make($request->all(),[
            'nomSecteur' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $secteur = Secteur::create([
            'nomSecteur' => $request->nomSecteur,
            'user_id' => Auth::user()->id,
        ]);

        return response()->json(['message' => 'Sector created successfully', 'secteur' => $secteur], 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(Secteur $secteur)
    {
        //
    }

    /**
     * @OA\Post(
     *      path="/api/secteur/destroy",
     *      operationId="destroysecteur",
     *      tags={"Secteurs"},
     *      summary="Supprimer un secteur",
     *      description="Supprime un secteur en fonction de l'ID fourni",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="secteur supprimé avec succès",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Non autorisé",
     *      ),
     * )
     */
    public function destroy($id)
    {

        if (!auth()->check() || auth()->user()->role_id !==1 ) {
            return response()->json(['message' => 'Non autorisé'], 401);
        }


        $secteur = Secteur::find($id);

        if (!$secteur) {
            return response()->json(['message' => 'Secteur not found'], 404);
        }

        $secteur->delete();

        return response()->json(['message' => 'Secteur deleted successfully'], 200);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Secteur $secteur)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Secteur $secteur)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
}
