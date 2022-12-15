<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendEmailPost;
use App\Models\Email;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class EmailController extends Controller
{
    /**
     * @param  SendEmailPost  $request
     * @param  Email  $email
     * @return JsonResponse
     */
    public function send(SendEmailPost $request, Email $email): JsonResponse
    {
        $requestBody = $request->validated();

        $emailSent = $email->create(
            $requestBody['from'],
            $requestBody['to'],
            $requestBody['subject'],
            $requestBody['htmlPart'],
        )->send();

        return response()
            ->json([ 'data' => $emailSent ])
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }
}
