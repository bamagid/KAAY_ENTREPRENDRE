<?php

namespace App\Http\Controllers;

use App\Models\Ressource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Ressources",
 *     description="Endpoints pour la gestion des ressources "
 * )
 */

class RessourceController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/ressources",
     *      operationId="getRessources",
     *      tags={"Ressources"},
     *      summary="Obtenir la liste des ressources",
     *      description="Récupère la liste de toutes les ressources non supprimées.",
     *      @OA\Response(
     *          response=200,
     *          description="Liste des ressources récupérée avec succès"
     *      ),
     * )
     */

    public function index()
    {
        $ressources = Ressource::where('is_deleted', 0)->get();
        return $ressources;
    }
    /**
     * @OA\Post(
     *      path="/api/ajouter-ressource",
     *      operationId="ajouterRessource",
     *      tags={"Ressources"},
     *      summary="Ajouter une nouvelle ressource",
     *      description="Ajoute une nouvelle ressource avec les détails fournis.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="titre", type="string"),
     *              @OA\Property(property="description", type="string"),
     *              @OA\Property(property="image", type="string", format="binary"),
     *              @OA\Property(property="lien", type="string"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Ressource ajoutée avec succès"
     *      ),
     * )
     */
    public function ajouterRessource(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'titre' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'lien' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        // Gestion du téléchargement de l'image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $imageName, 'public');
        }

        // Vérification de l'authentification de l'utilisateur
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        // Création de la ressource
        $ressource = new Ressource([
            'titre' => $request->titre,
            'description' => $request->description,
            'image' => $imagePath,
            'lien' => $request->lien,
        ]);

        // Attribution de l'ID de l'utilisateur à la ressource
        $ressource->user_id = $user->id;

        // Sauvegarde de la ressource
        $ressource->save();

        // Réponse JSON
        return response()->json(['message' => 'Ressource ajoutée avec succès'], 201);
    }

    /**
     * @OA\Post(
     *      path="/api/ressources/{id}",
     *      operationId="modifierRessource",
     *      tags={"Ressources"},
     *      summary="Modifier une ressource existante",
     *      description="Modifie les détails d'une ressource existante en fonction de l'ID fourni.",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID de la ressource",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="titre", type="string"),
     *              @OA\Property(property="description", type="string"),
     *              @OA\Property(property="image", type="string", format="binary"),
     *              @OA\Property(property="lien", type="string"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Ressource modifiée avec succès"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Ressource non trouvée",
     *      ),
     * )
     */
    public function modifierRessource(Request $request, $id)
    {
        if (!auth()->user()) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        $validator=Validator::make($request->all(),[
            'titre' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|string',
            'lien' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $ressource = Ressource::find($id);

        if (!$ressource) {
            return response()->json(['message' => 'Ressource non trouvée'], 404);
        }
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $imageName, 'public');
        }
        $ressource->fill([
            'titre' => $request->titre,
            'description' => $request->description,
            'image' =>  $imagePath,
            'lien' => $request->lien,
        ]);

        return response()->json(['message' => 'Ressource modifiée avec succès'], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    /**
     * @OA\Delete(
     *      path="/api/ressources/{id}",
     *      operationId="supprimerRessource",
     *      tags={"Ressources"},
     *      summary="Supprimer une ressource",
     *      description="Supprime une ressource en fonction de l'ID fourni.",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID de la ressource",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Ressource supprimée avec succès",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Ressource non trouvée",
     *      ),
     * )
     */
    public function supprimerRessource($id)
    {

        if (!auth()->user()) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        $ressource = Ressource::find($id);

        if (!$ressource) {
            return response()->json(['message' => 'Ressource non trouvée'], 404);
        }



        $ressource->delete();

        return response()->json(['message' => 'Ressource supprimée avec succès'], 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * @OA\Get(
     *      path="/api/ressource",
     *      operationId="getRessource",
     *      tags={"Ressources"},
     *      summary="Afficher une ressource",
     *      description="Affiche les détails d'une ressource en fonction de l'ID fourni.",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID de la ressource",
     *          required=true,
     *          in="query",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Détails de la ressource récupérés avec succès"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Ressource non trouvée",
     *      ),
     * )
     */
    public function show(Request $request)
    {
        $ressource = Ressource::findOrFail($request->id);
        return response()->json([
            "message"=>"Voici la resource que vous chercher",
            "ressource"=>$ressource
            ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ressource $ressource)
    {
        //
    }
    /**
     * @OA\Post(
     *      path="/api/ressource/archive",
     *      operationId="archiverRessource",
     *      tags={"Ressources"},
     *      summary="Archiver une ressource",
     *      description="Archive une ressource en fonction de l'ID fourni.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Ressource archivée avec succès"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Ressource non trouvée",
     *      ),
     * )
     */
    public function archive(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Non autorisé'], 401);
        }
        $ressource = Ressource::findOrFail($request->id);
        if (auth()->user()->id !== $ressource->user_id) {
            $ressource->is_deleted = true;
            $ressource->save();
            return response()->json([
                "message"=>"Ressource archivé avec succés",
                "ressource"=>$ressource
            ]);
        }
      
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ressource $ressource)
    {
        //
    }
}
