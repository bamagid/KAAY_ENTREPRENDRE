<?php

use App\Http\Controllers\CommentaireController;
use App\Http\Controllers\EtudeCasController;
use App\Http\Controllers\ReponseController;
use App\Http\Controllers\GuideController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SecteurController;
use App\Http\Controllers\EvenementController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\RessourceController;
use App\Http\Controllers\NewsletterSubscriptionController;
use App\Http\Controllers\phaseController;
use App\Http\Controllers\SearchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//ajouter un utilisateur 'entrepreneur novice'
Route::post('/ajouter-utilisateur-entrepreneur-novice', [UserController::class, 'ajouterUtilisateurEntrepreneurNovice']);
//ajouter un utilisateur 'entrepreneur experimente'
Route::post('/ajouter-utilisateur-entrepreneur-experimente', [UserController::class, 'ajouterUtilisateurEntrepreneurExperimente']);
//ajouter un utilisateur 'admin'
Route::post('/ajouter-utilisateur-admin', [UserController::class, 'ajouterUtilisateurAdmin']);
//se connecter
Route::post('login', [UserController::class, 'login']);
//verifier si un email existe
Route::post('verifMail', [UserController::class, 'verifMail']);
//reinitialisation du mot de passe
Route::post('resetPassword/{user}', [UserController::class, 'resetPassword']);
//afficher guide
Route::get('/index', [GuideController::class, 'index']);
//afficher phases
Route::get('/phases', [PhaseController::class, 'index']);
//afficher une phase
Route::get('/phase/{id}', [PhaseController::class, 'show']);
//lister les forums
Route::get('/forums', [ForumController::class, 'index']);
Route::get('/events', [EvenementController::class, 'index']);
//lister tous les commentaires d'un forum
Route::get('/commentaires', [CommentaireController::class, 'index']);
//lister  commentaire d'un forum
Route::post('/commentaire', [CommentaireController::class, 'show']);
//lister toutes les reponses d'un forum
Route::get('/reponses', [ReponseController::class, 'index']);
//lister  une reponse particulier d'un forum
Route::post('/reponse', [ReponseController::class, 'show']);
//afficher le forum selectionne
Route::post('/forum', [ForumController::class, 'show'])->name('forum.show');
Route::get('/events/{id}', [EvenementController::class, 'show']);
//souscrire a la newsletter
Route::post('/subscribe-newsletter', [NewsletterSubscriptionController::class, 'subscribe']);
//faire une recherche
Route::get('/search', [SearchController::class, 'search']);
Route::get('/index_cas', [EtudeCasController::class, 'index']);
//affichage du guide
Route::get('/guides', [GuideController::class, 'index']);
//afficher l'utilisateur connecté
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:api')->group(function () {
    /**Gestion etudes cas */
    // ajout cas pour
    Route::post('create', [EtudeCasController::class, 'create']);
    //modifier une etude cas
    Route::post('update_etudeCas', [EtudeCasController::class, 'update']);
    //archivier une etude cas
    Route::post('etude-cas/archive/{id}', [EtudeCasController::class, 'archive']);
    //supprimer une etude cas
    Route::post('etude-cas/delete/{id}', [EtudeCasController::class, 'delete']);
    /* gestion des commentaires du forums */
    //ajouter un commentaire a un forum
    Route::post('/commentaire/create', [CommentaireController::class, 'store'])->name('commentaire.add');

    //modifier un commentaire d'un forum
    Route::post('/commentaire/edit', [CommentaireController::class, 'update'])->name('commentaire.edit');
    //archiver un commentaire de forum
    Route::post('/commentaire/archive', [CommentaireController::class, 'archiveCommentaire'])->name('commentaire.archive');
    /* gestion des reponses du forums */
    //ajouter une reponse a un forum
    Route::post('/reponse/create', [ReponseController::class, 'store'])->name('reponse.add');
    //modifier une reponse d'un forum
    Route::post('/reponse/edit', [ReponseController::class, 'update'])->name('reponse.edit');
    //archiver une reponse de forum
    Route::post('/reponse/archive', [ReponseController::class, 'archivereponse'])->name('reponse.archive');
    /* Gestion des utilisatueurs  */
    //modification du profile d'un entrepreneur novice
    Route::post('/entrepreneur-novice/profile', [UserController::class, 'updateProfile']);
    //modification du profile d'un entrepreneur eperimenté
    Route::post('/entrepreneur-experimente/profile', [UserController::class, 'updateProfileExperimente']);
    //modifier le mot de passe d'un utilisateur
    Route::post('/modifier-mot-de-passe', [UserController::class, 'modifierMotDePasse']);
    //reinitialiser
    Route::post('/reinitialiser-mot-de-passe', [UserController::class, 'reinitialiserMotDePasse']);
    // Route::post('login', [UserController::class, 'login']);
    //se deconnecter
    Route::get('deconnecter', [UserController::class, 'deconnect']);
});

Route::middleware(['auth:api', 'admin'])->group(function () {
    /** Gestion des phases d'un guide */
    //ajouter phase
    Route::post('/create_phase', [PhaseController::class, 'create']);
    //modifier le phase
    Route::post('/update_phase/{id}', [PhaseController::class, 'update']);
    //archiver un phase
    Route::post('/archiver_phase/{id}', [phaseController::class, 'archiver_phase']);
    //ajouter guide
    Route::post('/create_guide', [GuideController::class, 'create']);
    //modifier le guide
    Route::post('/update/{id}', [GuideController::class, 'update']);
    //archiver un guide
    Route::post('/archiver_guide/{id}', [GuideController::class, 'archiver_guide']);
    /* gestion des forums*/
    //creer un forum
    Route::post('/forum/create', [ForumController::class, 'store'])->name('forum.create');
    //supprimer un forum
    Route::post('/forum/delete', [ForumController::class, 'destroy'])->name('forum.delete');
    //modifier un forum
    Route::post('forum/edit', [ForumController::class, 'update'])->name('forum.edit');
    //archiver une rubrique d'un forum
    Route::post('/forum/archiveRubrique', [ForumController::class, 'archiveRubrique'])->name('forum.archiveRubrique');
    //supprimer un commentaire d'un forum
    Route::post('/commentaire/delete', [CommentaireController::class, 'destroy'])->name('commentaire.delete');
    //supprimer une reponse d'un forum
    Route::post('/reponse/delete', [ReponseController::class, 'destroy'])->name('reponse.delete');
    //modification du profile d'un admin
    Route::post('/admin/profile', [UserController::class, 'UpdateAdmin']);
    //blockage d'un utilisateur par l'admin
    Route::post('/admin/block-account/{userId}', [UserController::class, 'toggleBlockAccount']);
    //ajouter un role
    Route::post('/ajouter-role', [UserController::class, 'ajouterRole']);
    //modifier le profile d'un admin
    Route::post('/admin/profile', [UserController::class, 'UpdateAdmin']);
    /* Gestion des ressources */
    //ajouter une ressource
    Route::post('ajouter-ressource', [RessourceController::class, 'ajouterRessource'])->name('ajouter-ressource');
    //modifier une ressource
    Route::post('/ressources/{id}', [RessourceController::class, 'modifierRessource']);
    //supprimer une ressource
    Route::delete('/ressources/{id}', [RessourceController::class, 'supprimerRessource']);
    //afficher toutes les ressources disponnibles
    Route::post('/ressources', [RessourceController::class, 'index']);
    //afficher une ressource en particulier
    Route::post('/ressource', [RessourceController::class, 'show']);
    //archiver une ressource
    Route::post('/ressource/archive', [RessourceController::class, 'archiveressource']);

    Route::post('ajouter-ressource', [RessourceController::class, 'ajouterRessource'])->name('ajouter-ressource');
    /* Gestion des evenements */
    //route pour evenement

    Route::get('/events/{id}', [EvenementController::class, 'show']);
    Route::post('/events', [EvenementController::class, 'store']);
    Route::post('/events/{id}', [EvenementController::class, 'update']);
    Route::delete('/events/{id}', [EvenementController::class, 'destroy']);

    Route::delete('/secteurs/{id}', [SecteurController::class, 'destroy']);
});

Route::middleware(['web', 'auth', 'checkStatus'])->group(function () {
    // mes routes quand lutilisateur est bloquer pour lui interdire certiane partied du site
});
