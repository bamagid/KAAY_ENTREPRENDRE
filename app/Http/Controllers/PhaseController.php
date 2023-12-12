<?php

namespace App\Http\Controllers;

use App\Models\Phase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Phases",
 *     description="Endpoints pour la gestion des phases "
 * )
 */
class phaseController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/phase/{id}",
     *      operationId="getPhase",
     *      tags={"Phases"},
     *      summary="Récupérer une phase en particulier",
     *      description="Récupère la phase specifier avec l'id",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID de la phase à mettre à jour",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Liste des phases récupérée avec succès"
     *      ),
     * )
     */
    public function show(int $id)
    {
        $phase = Phase::findOrFail($id);
        return response()->json(['message' => 'phases trouvé avec succès', 'phases' => $phase], 200);
    }
    /**
     * @OA\Get(
     *      path="/api/phases",
     *      operationId="getPhases",
     *      tags={"Phases"},
     *      summary="Récupérer la liste des phases",
     *      description="Récupère la liste de toutes les phases",
     *      @OA\Response(
     *          response=200,
     *          description="Liste des phases récupérée avec succès"
     *      ),
     * )
     */
    public function index()
    {
        $phases = Phase::where('is_deleted', 0)->get();
        return response()->json(['message' => 'phases trouvé avec succès', 'phases' => $phases], 200);
    }

    /**
     * @OA\Post(
     *      path="/api/create_phase",
     *      operationId="createphase",
     *      tags={"Phases"},
     *      summary="Ajouter une nouvelle phase",
     *      description="Ajoute une nouvelle phase avec les détails fournis",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="titre", type="string"),
     *              @OA\Property(property="description", type="string"),
     *              @OA\Property(property="guide_id", type="integer"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="phase créé avec succès"
     *      ),
     * )
     */
    public function create(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'titre' => 'required',
            'description' => 'required',
            'guide_id' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $phases=$validator->toarray();
        $phase = new Phase($phases);
        $phase->save();

        return response()->json(['message' => 'phase ajouté avec succès', 'phase' => $phase], 200);
    }

    /**
     * @OA\Post(
     *      path="/api/update_phase/{id}",
     *      operationId="updatephase",
     *      tags={"Phases"},
     *      summary="Mettre à jour une phase existant",
     *      description="Met à jour les détails d'une phase existant en fonction de l'ID fourni",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID de la phase à mettre à jour",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="titre", type="string"),
     *              @OA\Property(property="description", type="string"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="phase mise à jour avec succès"
     *      ),
     * )
     */
    public function update(Request $request, string $id)
    {
        $validator=Validator::make($request->all(),[
            'titre' => 'required',
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $phase = Phase::find($id);
        $phase->titre = $request->titre;
        $phase->description = $request->description;
        $phase->save();

        return response()->json(['message' => 'phase modifié avec succès', 'phase' => $phase], 200);
    }

    /**
     * @OA\Post(
     *      path="/api/archiver_phase/{id}",
     *      operationId="archivephase",
     *      tags={"Phases"},
     *      summary="Archiver une phase",
     *      description="Archive une phase en fonction de l'ID fourni",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID du phase à archiver",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="phase archivé avec succès"
     *      ),
     * )
     */
    public function archiver_phase(Request $request, int $id)
    {
        $phase = Phase::find($id);
        $phase->is_deleted = true;
        $phase->save();

        return response()->json(['message' => 'phase archivé avec succès', 'phase' => $phase], 200);
    }
}
