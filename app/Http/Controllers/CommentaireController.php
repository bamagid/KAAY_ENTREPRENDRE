<?php

namespace App\Http\Controllers;

use App\Models\Commentaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Commentaires",
 *     description="Endpoints pour la gestion des commentaires du forum"
 * )
 */
class CommentaireController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/commentaires",
     *     summary="Lister tous les commentaires d'un forum",
     *     tags={"Commentaires"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste de commentaires",
     *     ),
     * )
     */
    public function index()
    {
        $commentaires = Commentaire::where('is_deleted', 0)->get();
        return response()->json(["message" => "voici les commentaires ", 'commentaires' => $commentaires]);
    }

    /**
     * @OA\Post(
     *     path="/api/commentaire",
     *     summary="Lister un commentaire d'un forum",
     *     tags={"Commentaires"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du commentaire",
     *     ),
     * )
     */
    public function show(Request $request)
    {
        $commentaire = Commentaire::findOrFail($request->input('id'));
        if ($commentaire) {
            $reponses = $commentaire->reponses;
            foreach ($reponses as $reponse) {
                $reponse->user;
            }

            return response()->json([
                "message" => "voici le commentaire que vous chercher et les reponses qu'il detient",
                "auteur du commentaire" => $commentaire->user,
                "commentaire" => $commentaire,
                "reponses" => $reponses
                // "Auteur de la reponse"
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/commentaire/create",
     *     summary="Ajouter un commentaire à un forum",
     *     tags={"Commentaires"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="contenu", type="string", example="Contenu du commentaire"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Le commentaire est bien ajouté",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé",
     *     ),
     * )
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Non autorisé'], 401);
        }
        $validator = Validator::make(
            $request->all(),
            [
                'contenu' => 'required|string|min:3',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $commentaire = Commentaire::create(
            [
                'contenu' => $request->contenu,
                'user_id' => Auth::user()->id,
            ]
        );
        return response()->json([
            'message' => "Le commentaire est bien ajouté",
            "commentaire" => $commentaire
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/commentaire/edit",
     *     summary="Modifier un commentaire d'un forum",
     *     tags={"Commentaires"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="contenu", type="string", example="Nouveau contenu du commentaire"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Le commentaire est bien modifié",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé",
     *     ),
     * )
     */
    public function update(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Non autorisé'], 401);
        }
        $validator = Validator::make(
            $request->all(),
            [
                'contenu' => 'required|string|min:3',
                'id' => 'required|numeric',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $commentaire = Commentaire::findOrFail($request->input('id'));
        if ($commentaire->user_id === Auth::user()->id) {
            $commentaire->contenu = $request->contenu;
            $commentaire->update();
            return response()->json([
                'message' => "Le commentaire est bien modifié",
                "commentaire" => $commentaire
            ]);
        } else {
            return response()->json(['error' => "Vous n'avez pas le droit de modifier ce commentaire"], 401);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/commentaire/archive",
     *     summary="Archiver un commentaire de forum",
     *     tags={"Commentaires"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Le commentaire est bien archivé",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé",
     *     ),
     * )
     */
    public function archiveCommentaire(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Non autorisé'], 401);
        }
        $validator = Validator::make(
            $request->all(),
            [
                'id' => 'required|numeric',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $commentaire = Commentaire::findOrFail($request->input('id'));
        if ($commentaire->user_id === Auth::user()->id) {
            $commentaire->is_deleted = true;
            $commentaire->update();
            return response()->json([
                'message' => "Le commentaire est bien archivé",
                "commentaire" => $commentaire
            ]);
        } else {
            return response()->json(['error' => "Vous n'avez pas le droit de supprimer ce commentaire"], 401);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/commentaire/delete",
     *     summary="Supprimer un commentaire d'un forum",
     *     tags={"Commentaires"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Le commentaire est bien supprimé",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé",
     *     ),
     * )
     */
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Non autorisé'], 401);
        }
        $user = Auth::user();
        if ($user->role_id === 1) {
            $commentaire = Commentaire::findOrFail($request->input('id'));
            $commentaire->delete();
            return response()->json(['message' => "La commentaire est bien supprimé"]);
        } else {
            return response()->json(['error' => "Vous n'avez pas les droits pour effectuer cette action"], 401);
        }
    }
}
