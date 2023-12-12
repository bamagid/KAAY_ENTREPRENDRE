<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
 /**
     * @OA\Tag(
     *     name="Roles",
     *     description="Endpoints pour la gestion des rôles."
     * )
     */
class RoleController extends Controller
{

    /**
     * @OA\Get(
     *      path="/api/roles",
     *      operationId="getRoles",
     *      tags={"Roles"},
     *      summary="Obtenir la liste des rôles",
     *      description="Récupère la liste de tous les rôles.",
     *      @OA\Response(
     *          response=200,
     *          description="Liste des rôles récupérée avec succès"
     *      ),
     *      security={
     *          {"api_key": {}}
     *      }
     * )
     */
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Role $role)
    {
        //
    }
    public function update(Request $request, Role $role)
    {
        //
    }
    public function destroy(Role $role)
    {
        //
    }
}
