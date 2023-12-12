<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;
/**
 * @OA\Tag(
 *     name="Guides",
 *     description="Endpoints pour la gestion des guides "
 * )
 */
class GuideController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/guides",
     *      operationId="getGuides",
     *      tags={"Guides"},
     *      summary="Récupérer la liste des guides",
     *      description="Récupère  le guide",
     *      @OA\Response(
     *          response=200,
     *          description="Guides récupérée avec succès"
     *      ),
     * )
     */
    public function index()
    {
        $guides = Guide::all();
        return response()->json([
            "La listes de toutes les guides "=>$guides
        ], 200);
    }

    /**
     * @OA\Post(
     *      path="/api/guide/create",
     *      operationId="createGuide",
     *      tags={"Guides"},
     *      summary="Ajouter un nouveau guide",
     *      description="Ajoute un nouveau guide avec les détails fournis",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="titre", type="string"),
     *              @OA\Property(property="contenu", type="string"),
     *              @OA\Property(property="phases", type="string"),
     *              @OA\Property(property="reaction", type="string"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Guide créé avec succès"
     *      ),
     * )
     */
    public function create(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'titre' => 'required',
            'contenu' => 'required',
            'reaction' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $guide = new Guide();
        $guide->titre=$request->titre;
        $guide->titre=$request->contenu;
        $guide->reaction=$request->reaction;
        $guide->save();

        return response()->json(['message' => 'Guide ajouté avec succès', 'guide' => $guide], 200);
    }

    /**
     * @OA\Post(
     *      path="/api/guide/update/{id}",
     *      operationId="updateGuide",
     *      tags={"Guides"},
     *      summary="Mettre à jour un guide existant",
     *      description="Met à jour les détails d'un guide existant en fonction de l'ID fourni",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID du guide à mettre à jour",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="titre", type="string"),
     *              @OA\Property(property="contenu", type="string"),
     *              @OA\Property(property="phases", type="string"),
     *              @OA\Property(property="reaction", type="string"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Guide mis à jour avec succès"
     *      ),
     * )
     */
    public function update(Request $request, string $id)
    {
        $validator=Validator::make($request->all(),[
            'titre' => 'required',
            'contenu' => 'required',
            'reaction' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $guide = Guide::find($id);
        $guide->titre = $request->titre;
        $guide->contenu = $request->contenu;
        $guide->reaction = $request->reaction;
        $guide->save();

        return response()->json(['message' => 'Guide modifié avec succès', 'guide' => $guide], 200);
    }

    /**
     * @OA\Post(
     *      path="/api/guide/archiver_guide/{id}",
     *      operationId="archiveGuide",
     *      tags={"Guides"},
     *      summary="Archiver un guide",
     *      description="Archive un guide en fonction de l'ID fourni",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID du guide à archiver",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Guide archivé avec succès"
     *      ),
     * )
     */
    public function archiver_guide(Request $request, int $id)
    {
        $guide = Guide::find($id);
        $guide->is_deleted = true;
        $guide->save();

        return response()->json(['message' => 'Guide archivé avec succès', 'guide' => $guide], 200);
    }
}
