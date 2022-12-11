<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendEmailPost;
use Illuminate\Http\JsonResponse;

class EmailController extends Controller
{
    public function send(SendEmailPost $request): JsonResponse
    {
        $requestBody = $request->validated();

        return response()
            ->json([ 'data' => $requestBody ])
            ->setStatusCode(200);
    }
}
