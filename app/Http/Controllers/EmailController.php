<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendEmailPost;
use App\Models\Email;
use Illuminate\Http\JsonResponse;

class EmailController extends Controller
{
    /**
     * @param  SendEmailPost  $request
     * @return JsonResponse
     */
    public function send(SendEmailPost $request): JsonResponse
    {
        $requestBody = $request->validated();

        $emailSent = (new Email(
            $requestBody['from'],
            $requestBody['to'],
            $requestBody['subject'],
            $requestBody['htmlPart'],
        ))->send();

        return response()
            ->json([ 'data' => $emailSent ])
            ->setStatusCode(202);
    }
}
