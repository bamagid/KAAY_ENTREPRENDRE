<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
   /**
 * @OA\Info(
 *   title="Api Kaay entreprendre",
 *   version="1.0.0",
 *   description="Cet api va nous permettre de faire communiquer la partie front et la partie back de notre plateforme"
 * )
 */
class Controller extends BaseController
{
  /**
 * @OA\SecurityScheme(
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 *      securityScheme="BearerAuth"
 * )
 */
    use AuthorizesRequests, ValidatesRequests;
 
}
