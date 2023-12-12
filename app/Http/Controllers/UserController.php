<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Notifications\MotDePasseOublié;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Utilisateurs",
 *     description="Endpoints pour la gestion des utilisateurs."
 * )
 */
class UserController extends Controller
{

    /**
     * @OA\Post(
     *      path="/api/ajouter-role",
     *      operationId="createRole",
     *      tags={"Roles"},
     *      summary="Ajouter un role",
     *      description="Ajout d'un role par l'admin",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="nomRole", type="string"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Role ajouté avec succés"
     *      ),
     * )
     */
    public function ajouterRole(Request $request)
    {
        $request->validate([
            'nomRole' => 'required|string|unique:roles',
        ]);

        $role = Role::create([
            'nomRole' => $request->nomRole,
        ]);

        return response()->json(['message' => 'Rôle ajouté avec succès', 'role' => $role], 201);
    }

    /**
     * @OA\Post(
     *      path="/api/ajouter-utilisateur-entrepreneur-novice",
     *      operationId="createEntrepreneurNovice",
     *      tags={"Utilisateurs"},
     *      summary="Inscrire un entrepreneur novice",
     *      description="Inscription d'un entrepreneur novice  avec les détails fournis",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="nom", type="string"),
     *              @OA\Property(property="prenom", type="string"),
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="password", type="string"),
     *              @OA\Property(property="adresse", type="string"),
     *              @OA\Property(property="region", type="string"),
     *              @OA\Property(property="statut", type="string"),
     *              @OA\Property(property="image", type="file"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Entrepreneur inscrit avec succès"
     *      ),
     * 
     * )
     */

    public function ajouterUtilisateurEntrepreneurNovice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => ['required', 'string', 'min:4', 'regex:/^[a-zA-Z]+$/'],
            'prenom' => ['required', 'string', 'min:4', 'regex:/^[a-zA-Z]+$/'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => Rules\Password::defaults(),
            'adresse' => ['required', 'string', 'regex:/^[a-zA-Z0-9 ]+$/'],
            'region' => ['required', 'string', 'regex:/^[a-zA-Z ]+$/'],
            'statut' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $imageName, 'public');
        }
        $roleEntrepreneurNovice = Role::where('nomRole', 'novice')->first();
        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'adresse' => $request->adresse,
            'region' => $request->region,
            'role_id' => $roleEntrepreneurNovice->id,
            'statut' => $request->statut,
            'image' => $imagePath,
            'progression' => $request->progression
        ]);

        $user->roles()->attach($roleEntrepreneurNovice);

        return response()->json(['message' => 'Utilisateur ajouté avec succès'], 201);
    }

    /**
     * @OA\Post(
     *      path="/api/ajouter-utilisateur-entrepreneur-expreimente",
     *      operationId="createEntrepreneurExpreimente",
     *      tags={"Utilisateurs"},
     *      summary="Inscrire un entrepreneur expreimenté",
     *      description="Inscription d'un entrepreneur expreimenté  avec les détails fournis",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="nom", type="string"),
     *              @OA\Property(property="prenom", type="string"),
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="password", type="string"),
     *              @OA\Property(property="adresse", type="string"),
     *              @OA\Property(property="region", type="string"),
     *              @OA\Property(property="statut", type="string"),
     *              @OA\Property(property="image", type="file"),
     *              @OA\Property(property="experience", type="string"),
     *              @OA\Property(property="activite", type="string"),
     *              @OA\Property(property="realisation", type="string"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Entrepreneur inscrit avec succès"
     *      ),
     * )
     */

    public function ajouterUtilisateurEntrepreneurExperimente(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|min:2|regex:/^[a-zA-Z]+$/',
            'prenom' => 'required|string|min:4|regex:/^[a-zA-Z]+$/',
            'email' => 'required|email|unique:users,email',
            'password' =>Rules\Password::defaults(),
            'adresse' => 'required|string|regex:/^[a-zA-Z0-9 ]+$/',
            'region' => 'required|string|regex:/^[a-zA-Z ]+$/',
            'statut' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $imagePath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $imageName, 'public');
        }

        $roleEntrepreneurExperimente = Role::where('nomRole', 'entrepreneur_experimente')->first();

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'adresse' => $request->adresse,
            'region' => $request->region,
            'role_id' => $request->role_id,
            'statut' => $request->statut,
            'image' => $imagePath,
            'experience' => $request->experience,
            'activite' => $request->activite,
            'realisation' => $request->realisation
        ]);

        $user->roles()->attach($roleEntrepreneurExperimente);

        return response()->json(['message' => 'Entrepreneur expérimenté ajouté avec succès'], 201);
    }

    /**
     * @OA\Post(
     *      path="/api/ajouter-utilisateur-admin",
     *      operationId="createAdmin",
     *      tags={"Utilisateurs"},
     *      summary="Inscrire un admin",
     *      description="Inscription d'un admin  avec les détails fournis",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="nom", type="string"),
     *              @OA\Property(property="prenom", type="string"),
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="password", type="string"),
     *              @OA\Property(property="statut", type="string"),
     *              @OA\Property(property="image", type="file"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Admin inscrit avec succès"
     *      ),
     * )
     */


    public function ajouterUtilisateurAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|min:2|regex:/^[a-zA-Z]+$/',
            'prenom' => 'required|string|min:4|regex:/^[a-zA-Z]+$/',
            'email' => 'required|email|unique:users,email',
            'password' =>Rules\Password::defaults(),

            'statut' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Ajoutez des règles pour la validation de l'image si nécessaire
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $imagePath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $imageName, 'public');
        }

        $roleAdmin = Role::where('nomRole', 'admin')->first();

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'adresse' => $request->adresse,
            'region' => $request->region,
            'role_id' => $request->role_id,
            'statut' => $request->statut,
            'image' => $imagePath
        ]);

        $user->roles()->attach($roleAdmin);

        return response()->json(['message' => 'Admin ajouté avec succès'], 201);
    }

    /**
     * @OA\Post(
     *      path="/api/login",
     *      operationId="login",
     *      tags={"Utilisateurs"},
     *      summary="Connecter un utilisateur",
     *      description="Connexion d'un utilisteurs  avec les détails fournis",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="password", type="string"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Utilisateur connecté avec succès"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Informations invalid"
     *      ),
     * )
     */
    public function login(Request $request)
    {

        // data validation
        $validator = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => Rules\Password::defaults()
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // JWTAuth
        $token = JWTAuth::attempt([
            "email" => $request->email,
            "password" => $request->password
        ]);

        if (!empty($token)) {

            return response()->json([
                "status" => true,
                "message" => "utilisateur connecter avec succe",
                "token" => $token
            ]);
        }

        return response()->json([
            "status" => false,
            "message" => "details invalide"
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/deconnecter",
     *      operationId="logout",
     *      tags={"Utilisateurs"},
     *      summary="Deconnecter un utilisateur",
     *      description="Deconnexion d'un utilisteurs  et invalidation de son token jwt",
     *      @OA\Response(
     *          response=200,
     *          description="Utilisateur deconnecté  avec succès"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Aucun utilisateur connecté"
     *      ),
     * )
     */

    public function deconnect()
    {
        //J'utilise la façade Auth pour faire la deconnexion
        Auth::logout();
        session()->invalidate();

        session()->regenerateToken();

        return redirect('/');
    }

    /**
     * @OA\Post(
     *      path="/api/entrepreneur-novice/profile",
     *      operationId="updateEntrepreneurNovice",
     *      tags={"Utilisateurs"},
     *      summary="Modifier les informations d'un entrepreneur novice",
     *      description="Modification des informations d'un entrepreneur novice  avec les détails fournis",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="nom", type="string"),
     *              @OA\Property(property="prenom", type="string"),
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="password", type="string"),
     *              @OA\Property(property="adresse", type="string"),
     *              @OA\Property(property="region", type="string"),
     *              @OA\Property(property="statut", type="string"),
     *              @OA\Property(property="image", type="file"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Informations mise a jour avec succès"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="L'utilisateur n'a pas été trouvé"
     *      ),
     * )
     */

    public function updateProfile(Request $request)
    {
        $entrepreneurNovice = auth()->user();

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $entrepreneurNovice->id,
            'password' => 'nullable|min:6',
            'adresse' => 'required|string',
            'region' => 'required|string',
            'statut' => 'required|string',

        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePath = $entrepreneurNovice->image;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $imageName, 'public');
        }

        $entrepreneurNovice->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $entrepreneurNovice->password,
            'adresse' => $request->adresse,
            'region' => $request->region,
            'statut' => $request->statut,
            'image' => $imagePath,

        ]);

        return response()->json(['message' => 'Profil mis à jour avec succès'], 200);
    }

    /**
     * @OA\Post(
     *      path="/api/entrepreneur-experimente/profile",
     *      operationId="updateEntrepreneurExpreimente",
     *      tags={"Utilisateurs"},
     *      summary="Modifier les informations d'un entrepreneur expreimenté",
     *      description="Modification des informations d'un entrepreneur expreimenté  avec les détails fournis",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="nom", type="string"),
     *              @OA\Property(property="prenom", type="string"),
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="password", type="string"),
     *              @OA\Property(property="adresse", type="string"),
     *              @OA\Property(property="region", type="string"),
     *              @OA\Property(property="statut", type="string"),
     *              @OA\Property(property="image", type="file"),
     *              @OA\Property(property="experience", type="string"),
     *              @OA\Property(property="activite", type="string"),
     *              @OA\Property(property="realisation", type="string"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Informations de l'entrepreneur modifié avec succès"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="L'utilisateur n'a pas été trouvé"
     *      ),
     * )
     */


    public function updateProfileExperimente(Request $request)
    {
        $entrepreneurExperimente = auth()->user();

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $entrepreneurExperimente->id,
            'password' => 'nullable|min:6',
            'adresse' => 'required|string',
            'region' => 'required|string',
            'statut' => 'required|string',
            'experience' => 'required|string',
            'activite' => 'required|string',
            'realisation' => 'required|string',

        ]);

        // $imagePath = $entrepreneurExperimente->image;
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $imageName, 'public');
        }

        $entrepreneurExperimente->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $entrepreneurExperimente->password,
            'adresse' => $request->adresse,
            'region' => $request->region,
            'statut' => $request->statut,
            'experience' => $request->experience,
            'activite' => $request->activite,
            'realisation' => $request->realisation,
            'image' => $imagePath,

        ]);

        return response()->json(['message' => 'Profil mis à jour avec succès'], 200);
    }

    /**
     * @OA\Post(
     *      path="/api/admin/profile",
     *      operationId="updateAdmin",
     *      tags={"Utilisateurs"},
     *      summary="Modifier les informations d'un admin",
     *      description="Modification des informations d'un admin  avec les détails fournis",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="nom", type="string"),
     *              @OA\Property(property="prenom", type="string"),
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="password", type="string"),
     *              @OA\Property(property="statut", type="string"),
     *              @OA\Property(property="image", type="file"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Informations admin modifié avec succès"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="L'utilisateur n'a pas été trouvé"
     *      ),
     * )
     */

    public function UpdateAdmin(Request $request)
    {


        $admin = auth()->user();

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'password' => 'nullable|min:6',
            'adresse' => 'required|string',
            'region' => 'required|string',
            'statut' => 'required|string',


        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $imagePath = $admin->image;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $imageName, 'public');
        }

        $admin->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $admin->password,
            'adresse' => $request->adresse,
            'region' => $request->region,


            'image' => $imagePath,

        ]);

        return response()->json(['message' => 'Profil mis à jour avec succès'], 200);
    }

    /**
     * @OA\Post(
     *      path="/api/admin/block-account/{userId}",
     *      operationId="Blockuser",
     *      tags={"Utilisateurs"},
     *      summary="bloquer un utilisateur",
     *      description="bloquer un utilisateur en specifiant son id",
     *
     *  @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID de l'utilisateur a bloquer",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Informations admin modifié avec succès"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="L'utilisateur n'a pas été trouvé"
     *      ),
     * )
     */

    public function toggleBlockAccount(int $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        $user->toggleStatus();

        $status = $user->statut === 'actif' ? 'débloqué' : 'bloqué';

        return response()->json(['message' => "Compte $status avec succès"], 200);
    }



    /**
     * @OA\Post(
     *      path="/api/modifier-mot-de-passe",
     *      operationId="updatePassword",
     *      tags={"Utilisateurs"},
     *      summary="Modifier le mot de passe d'un utilisateur",
     *      description="Modification du mot de passe d'un utilisateur  avec les détails fournis",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="ancien_password", type="string"),
     *              @OA\Property(property="nouveau_password", type="string"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Mot de passe mise a jour avec succés"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="L'utilisateur n'a pas été trouvé"
     *      ),
     * )
     */


    public function modifierMotDePasse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ancien_password' => 'required',
            'nouveau_password' =>Rules\Password::defaults(),
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        // Vérifiez que l'ancien mot de passe est correct
        if (!Hash::check($request->ancien_password, $user->password)) {
            return response()->json(['message' => 'Mot de passe actuel incorrect'], 401);
        }

        // Mettez à jour le mot de passe avec le nouveau
        $user->password = Hash::make($request->nouveau_password);
        // $user->notify(new MotDePasseOublié());
        $user->save();

        return response()->json(['message' => 'Mot de passe modifié avec succès'], 200);
    }

    /**
     * @OA\Post(
     *     path="/verifMail",
     *     summary="Vérifier si un email existe",
     *     tags={"Utilisateurs"},
     *     @OA\Response(
     *         response=200,
     *         description="L'email existe.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="exists", type="boolean", example=true),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="L'email n'existe pas.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="exists", type="boolean", example=false),
     *         ),
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="test@example.com"),
     *         )
     *     ),
     * )
     */

    public function verifMail(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            return response()->json([
                'status_code' => 200,
                'status_message' => 'Utilisateur trouvé',
                'user' => $user,
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/resetPassword/{user}",
     *     summary="Réinitialisation du mot de passe",
     *     tags={"Utilisateurs"},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur",
     *         @OA\Schema(type="integer", format="int64"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mot de passe réinitialisé avec succès.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Mot de passe réinitialisé avec succès."),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur non trouvé.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Utilisateur non trouvé."),
     *         ),
     *     ),
     * )
     */
    public function resetPassword(Request $request, User $user)
    {
        $user->password = $request->password;
        $user->save();
        if ($user) {
            return response()->json([
                'status_code' => 200,
                'status_message' => 'Votre mot de passe a été modifier',
                'user' => $user,
            ]);
        }
    }
}
