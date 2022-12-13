<?php

namespace App\Clients;

use Illuminate\Support\Facades\Log;
use Mailjet\Client;
use Mailjet\Resources;

class MailjetEmailClient implements EmailClient
{
    private Client $client;

    /**
     * @param  string  $key
     * @param  string  $secret
     * @param  bool  $performer
     * @param  string  $version
     */
    public function __construct(string $key, string $secret, bool $performer = true, string $version = 'v3.1')
    {
        $this->client = new Client(
            $key,
            $secret,
            $performer,
            [
                'version' => $version
            ]
        );
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param  array  $email
     * @return bool
     */
    public function send(array $email): bool
    {
        try {
            $response = $this->client->post(Resources::$Email, ['body' => [
                'Messages' => [
                    [
                        'From' => [
                            'Email' => $email['from']['email'],
                            'Name' => $email['from']['name'],
                        ],
                        'To' => [
                            [
                                'Email' => $email['to']['email'],
                                'Name' => $email['to']['name'],
                            ]
                        ],
                        'Subject' => $email['subject'],
                        'HTMLPart' => $email['subject'],
                        'CustomID' => $email['id'],
                    ]
                ]
            ]]);

            Log::info(sprintf('Mailjet response: %s %s', $response->getStatus(), json_encode($response->getBody())));

            return $response->success();
        } catch (\Exception $exception) {
            Log::error(sprintf('Mailjet send error: %s', $exception->getMessage()));

            return false;
        }
    }
}
