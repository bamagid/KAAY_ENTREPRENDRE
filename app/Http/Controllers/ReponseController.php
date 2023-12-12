<?php

namespace App\Http\Controllers;

use App\Models\Reponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;
/**
 * @OA\Tag(
 *     name="Réponses",
 *     description="Endpoints pour la gestion des réponses "
 * )
 */
class ReponseController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/reponses",
     *      operationId="getReponses",
     *      tags={"Réponses"},
     *      summary="Récupérer la liste des réponses",
     *      description="Récupère la liste de toutes les réponses",
     *      @OA\Response(
     *          response=200,
     *          description="Liste des réponses récupérée avec succès",
     *      ),
     * )
     */
    public function index()
    {
        $reponses = Reponse::where('is_deleted', 0)->get();
        return response()->json(['message'=>"voici toutes les reponses du site",'reponses'=>$reponses]);
    }

    /**
     * @OA\Post(
     *      path="/api/reponse/create",
     *      operationId="createReponse",
     *      tags={"Réponses"},
     *      summary="Ajouter une nouvelle réponse",
     *      description="Ajoute une nouvelle réponse avec les détails fournis",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="contenu", type="string"),
     *              @OA\Property(property="commentaire_id", type="numeric"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Réponse ajoutée avec succès",
     *      ),
     * )
     */
    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'contenu' => 'required|string|min:3',
            'commentaire_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

      $reponse= Reponse::create([
            'contenu' => $request->contenu,
            'user_id' => auth()->user()->id,
            'commentaire_id' => $request->input('commentaire_id'),
        ]);

        return response()->json(['message' => "La réponse a bien été ajoutée",'reponse'=>$reponse]);
    }

    /**
     * @OA\Post(
     *      path="/api/reponse/edit",
     *      operationId="updateReponse",
     *      tags={"Réponses"},
     *      summary="Mettre à jour une réponse existante",
     *      description="Met à jour les détails d'une réponse existante",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="contenu", type="string"),
     *              @OA\Property(property="id", type="numeric"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Réponse mise à jour avec succès",
     *      ),
     * )
     */
    public function update(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'contenu' => 'required|string|min:3',
            'id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $reponse = Reponse::findOrFail($request->input('id'));
        if ($reponse->user_id === auth()->user()->id) {
            $reponse->contenu = $request->contenu;
            $reponse->update();
            return response()->json(['message' => "La réponse a bien été modifiée",'reponse'=>$reponse]);
        } else {
            return response()->json(['error' => "Vous n'avez pas le droit de modifier cette réponse"], 401);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/reponse/archive",
     *      operationId="archiveReponse",
     *      tags={"Réponses"},
     *      summary="Archiver une réponse",
     *      description="Archive une réponse en fonction de l'ID fourni",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="numeric"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Réponse archivée avec succès",
     *      ),
     * )
     */
    public function archivereponse(Request $request)
    {
         if (!auth()->check() ) {
            return response()->json(['message' => 'Non autorisé'], 401);
        }
        $validator=Validator::make($request->all(),[
            'id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $reponse = Reponse::findOrFail($request->input('id'));
        if ($reponse->user_id === auth()->user()->id) {
            $reponse->is_deleted = true;
            $reponse->update();
            return response()->json(['message' => "La réponse a bien été archivée",'reponse'=>$reponse]);
        } else {
            return response()->json(['error' => "Vous n'avez pas le droit de supprimer cette réponse"], 401);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/reponse/delete",
     *      operationId="destroyReponse",
     *      tags={"Réponses"},
     *      summary="Supprimer une réponse",
     *      description="Supprime une réponse en fonction de l'ID fourni",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="numeric"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Réponse supprimée avec succès",
     *      ),
     * )
     */
    public function destroy(Request $request)
    {
        $user = auth()->user();
        if ($user->role_id === 1) {
            $validator=Validator::make($request->all(),[
                'id' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $reponse = Reponse::findOrFail($request->input('id'));
            $reponse->delete();
            return response()->json(['message' => "La réponse a bien été supprimée"]);
        } else {
            return response()->json(['error' => "Vous n'avez pas les droits pour effectuer cette action"], 401);
        }
    }
}
