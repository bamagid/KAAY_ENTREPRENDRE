<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;
/**
 * @OA\Tag(
 *     name="Forums",
 *     description="Endpoints pour la gestion des forums "
 * )
 */
class ForumController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/forums",
     *      operationId="getForums",
     *      tags={"Forums"},
     *      summary="Récupérer la liste des forums",
     *      description="Récupère la liste de tous les forums non supprimés",
     *      @OA\Response(
     *          response=200,
     *          description="Liste des forums récupérée avec succès"
     *      ),
     * )
     */
    public function index()
    {
        $forums = Forum::where('is_deleted', 0)->get();
        return $forums;
    }

    /**
     * @OA\Post(
     *      path="/api/forum",
     *      operationId="getForum",
     *      tags={"Forums"},
     *      summary="Afficher le forum sélectionné",
     *      description="Affiche les détails du forum spécifique en fonction de l'ID fourni",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Détails du forum récupérés avec succès",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Forum non trouvé",
     *      ),
     * )
     */
    public function show(Request $request)
    {
        $forum = Forum::findOrFail($request->input('id'));
        if ($forum) {
            return response()->json([
                "Voici le forum"=>$forum
            ]);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/forum/create",
     *      operationId="createForum",
     *      tags={"Forums"},
     *      summary="Ajouter un nouveau forum",
     *      description="Ajoute un nouveau forum avec les détails fournis",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="nomRubrique", type="string"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Forum créé avec succès",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Non autorisé",
     *      ),
     * )
     */
    public function store(Request $request)
    {
        if (!auth()->check() || auth()->user()->role_id !==1 ) {
            return response()->json(['message' => 'Non autorisé'], 401);
        }
        $validator=Validator::make($request->all(),[
            'nomRubrique' => 'required|string|min:3',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        if ($user->role_id === 1) {
            Forum::create([
                'rubrique' => $request->nomRubrique,
                'user_id' => $user->id,
            ]);

            return response()->json(['message' => "La rubrique est bien ajoutée"]);
        } else {
            return response()->json(['error' => "Vous n'avez pas les droits pour effectuer cette action"], 401);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/forum/update",
     *      operationId="updateForum",
     *      tags={"Forums"},
     *      summary="Mettre à jour un forum existant",
     *      description="Met à jour les détails d'un forum existant en fonction de l'ID fourni",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer"),
     *              @OA\Property(property="nomRubrique", type="string"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Forum mis à jour avec succès",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Non autorisé",
     *      ),
     * )
     */
    public function update(Request $request)
    {
        if (!auth()->check() || auth()->user()->role_id !==1 ) {
            return response()->json(['message' => 'Non autorisé'], 401);
        }
        $validator=Validator::make($request->all(),[
            'nomRubrique' => 'required|string|min:3',
            'id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = Auth::user();

        if ($user->role_id === 1) {
            $forum = Forum::findOrFail($request->input('id'));
            $forum->rubrique = $request->nomRubrique;
            $forum->update();

            return response()->json(['message' => "La rubrique est bien modifiée"]);
        } else {
            return response()->json(['error' => "Vous n'avez pas les droits pour effectuer cette action"], 401);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/forum/archive",
     *      operationId="archiveForum",
     *      tags={"Forums"},
     *      summary="Archiver un forum",
     *      description="Archive un forum en fonction de l'ID fourni",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Forum archivé avec succès",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Non autorisé",
     *      ),
     * )
     */
    public function archiveRubrique(Request $request)
    {
        if (!auth()->check() || auth()->user()->role_id !==1 ) {
            return response()->json(['message' => 'Non autorisé'], 401);
        }
        $user = Auth::user();

        if ($user->role_id === 1) {
            $forum = Forum::findOrFail($request->input('id'));
            $forum->is_deleted = true;
            $forum->update();

            return response()->json([
                'message' => "La rubrique a été bien archivée",
                "forum"=>$forum
        ]);
        } else {
            return response()->json(['error' => "Vous n'avez pas les droits pour effectuer cette action"], 401);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/forum/destroy",
     *      operationId="destroyForum",
     *      tags={"Forums"},
     *      summary="Supprimer un forum",
     *      description="Supprime un forum en fonction de l'ID fourni",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Forum supprimé avec succès",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Non autorisé",
     *      ),
     * )
     */
    public function destroy(Request $request)
    {
        if (!auth()->check() || auth()->user()->role_id !==1 ) {
            return response()->json(['message' => 'Non autorisé'], 401);
        }
        $user = Auth::user();

        if ($user->role_id === 1) {
            $forum = Forum::findOrFail($request->input('id'));
            $forum->delete();

            return response()->json(['message' => "La rubrique est bien supprimée"]);
        } else {
            return response()->json(['error' => "Vous n'avez pas les droits pour effectuer cette action"], 401);
        }
    }
}
