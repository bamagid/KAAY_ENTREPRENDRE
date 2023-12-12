<?php

namespace App\Http\Controllers;

use App\Models\Evenement;
use App\Models\Secteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;
/**
 * @OA\Tag(
 *     name="Evenements",
 *     description="Endpoints pour la gestion des evenements "
 * )
 */
class EvenementController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/events",
     *      operationId="getEvents",
     *      tags={"Evenements"},
     *      summary="Récupérer la liste des événements",
     *      description="Récupère la liste de tous les événements non supprimés",
     *      @OA\Response(
     *          response=200,
     *          description="Liste des événements récupérée avec succès"
     *      ),
     * )
     */
    public function index()
    {
        $events = Evenement::where('is_deleted', false)->get();
        return response()->json($events, 200);
    }

    /**
     * @OA\Get(
     *      path="/api/events/{id}",
     *      operationId="getEventById",
     *      tags={"Evenements"},
     *      summary="Récupérer un événement par ID",
     *      description="Récupère les détails d'un événement spécifique en fonction de l'ID fourni",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID de l'événement",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Détails de l'événement récupérés avec succès"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Événement non trouvé",
     *      ),
     * )
     */
    public function show($id)
    {
        $event = Evenement::findOrFail($id);
        return response()->json($event, 200);
    }

    /**
     * @OA\Post(
     *      path="/api/events",
     *      operationId="createEvent",
     *      tags={"Evenements"},
     *      summary="Créer un nouvel événement",
     *      description="Crée un nouvel événement avec les détails fournis",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="nomEvenement", type="string"),
     *              @OA\Property(property="type", type="string", enum={"en ligne", "presentiel"}),
     *              @OA\Property(property="dateEvenement", type="string", format="date"),
     *              @OA\Property(property="lieuEvenement", type="string"),
     *              @OA\Property(property="description", type="string"),
     *              @OA\Property(property="secteur_id", type="integer"),
     *              @OA\Property(property="image", type="string"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Événement créé avec succès"
     *      ),
     * )
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Non autorisé'], 401);
        }
        $user = auth()->user();

        $validator=Validator::make($request->all(),[
            'nomEvenement' => 'required|string',
            'type' => 'required|in:en ligne,presentiel',
            'dateEvenement' => 'required|date',
            'lieuEvenement' => 'required|string',
            'description' => 'required|string',
            'secteur_id' => 'required|exists:secteurs,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $imagePath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $imageName, 'public');
        }



        $event = Evenement::create([
            'nomEvenement' => $request->nomEvenement,
            'type' => $request->type,
            'dateEvenement' => $request->dateEvenement,
            'lieuEvenement' => $request->lieuEvenement,
            'image' => $imagePath,
            'description' => $request->description,
            'user_id' => $request->user_id,
            'secteur_id' => $request->secteur_id,
        ]);

        return response()->json(['message' => 'Evenement created successfully', 'event' => $event], 201);
    }

    /**
     * @OA\Post(
     *      path="/api/events/{id}",
     *      operationId="updateEvent",
     *      tags={"Evenements"},
     *      summary="Mettre à jour un événement existant",
     *      description="Met à jour les détails d'un événement existant en fonction de l'ID fourni",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID de l'événement",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="nomEvenement", type="string"),
     *              @OA\Property(property="type", type="string", enum={"en ligne", "presentiel"}),
     *              @OA\Property(property="dateEvenement", type="string", format="date"),
     *              @OA\Property(property="lieuEvenement", type="string"),
     *              @OA\Property(property="description", type="string"),
     *              @OA\Property(property="secteur_id", type="integer"),
     *              @OA\Property(property="image", type="string"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Événement mis à jour avec succès"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Événement non trouvé",
     *      ),
     * )
     */
    public function update(Request $request, $id)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Non autorisé'], 401);
        }
        $validator=Validator::make($request->all(),[
            'nomEvenement' => 'required|string',
            'type' => 'required|in:en ligne,presentiel',
            'dateEvenement' => 'required|date',
            'lieuEvenement' => 'required|string',
            'description' => 'required|string',
            'secteur_id' => 'required|exists:secteurs,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        if (!auth()->check()) {
            return response()->json(['message' => 'Non autorisé'], 401);
        }
        $event = Evenement::find($id);


        if (!$event) {
            return response()->json(['message' => 'Événement non trouvé'], 404);
        }

        $user = auth()->user();
        $imagePath = $event->image; // Utilisez l'image existante par défaut

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $imageName, 'public');
        }

        $event->update([
            'nomEvenement' => $request->nomEvenement,
            'type' => $request->type,
            'dateEvenement' => $request->dateEvenement,
            'lieuEvenement' => $request->lieuEvenement,
            'image' => $imagePath,
            'description' => $request->description,
            'user_id' => $user->id,
            'secteur_id' => $request->secteur_id,
        ]);

        return response()->json(['message' => 'Evenement updated successfully', 'event' => $event], 200);
    }

    /**
     * @OA\Delete(
     *      path="/api/events/{id}",
     *      operationId="deleteEvent",
     *      tags={"Evenements"},
     *      summary="Supprimer un événement",
     *      description="Supprime un événement en fonction de l'ID fourni",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID de l'événement",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Événement supprimé avec succès",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Événement non trouvé",
     *      ),
     * )
     */
    public function destroy($id)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Non autorisé'], 401);
        }

        $event = Evenement::findOrFail($id);

        if (!$event) {
            return response()->json(['message' => 'Événement non trouvé'], 404);
        }

        $event->delete();

        return response()->json(['message' => 'Evenement supprime avec succès'], 200);
    }
}
