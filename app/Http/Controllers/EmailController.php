<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendEmailPost;
use Illuminate\Http\JsonResponse;

class EmailController extends Controller
{
    public function send(SendEmailPost $request): JsonResponse
    {
        $requestBody = $request->validated();

        // Test RabbitMQ connection
        \Amqp::publish('', $request->getContent(), ['queue' => 'email_queue',]);

        return response()
            ->json([ 'data' => $requestBody ])
            ->setStatusCode(200);
    }
}
