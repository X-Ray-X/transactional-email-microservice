<?php

namespace App\Clients;

use App\Repositories\EmailLogRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Mailjet\Client;
use Mailjet\Resources;

class MailjetEmailClient implements EmailClient
{
    public const CLIENT_NAME = 'Mailjet';

    private EmailLogRepositoryInterface $emailLogRepository;
    private Client $client;

    /**
     * @param  EmailLogRepositoryInterface  $emailLogRepository
     * @param  string  $key
     * @param  string  $secret
     * @param  bool  $performer
     * @param  string  $version
     */
    public function __construct(EmailLogRepositoryInterface $emailLogRepository, string $key, string $secret, bool $performer = true, string $version = 'v3.1')
    {
        $this->emailLogRepository = $emailLogRepository;

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
     * @return string
     */
    public function __toString(): string
    {
        return self::CLIENT_NAME;
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

            $this->emailLogRepository->update(
                $email['id'],
                [
                    'email_provider' => self::CLIENT_NAME,
                    'response' => json_encode($response->getBody()),
                ]
            );

            Log::debug(sprintf('Mailjet response: %s %s', $response->getStatus(), json_encode($response->getBody())));

            return $response->success();
        } catch (\Exception $exception) {
            Log::error(sprintf('Mailjet send error: %s', $exception->getMessage()));

            return false;
        }
    }
}
