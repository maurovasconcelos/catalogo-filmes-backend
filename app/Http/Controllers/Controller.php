<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="API de Catálogo de Filmes",
 *     version="1.0.0",
 *     description="API para gerenciamento de filmes favoritos",
 *     @OA\Contact(
 *         email="contato@exemplo.com",
 *         name="Suporte API"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * @OA\Server(
 *     url="/api",
 *     description="API Server"
 * )
 * @OA\Tag(
 *     name="Favoritos",
 *     description="Operações relacionadas aos filmes favoritos"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
