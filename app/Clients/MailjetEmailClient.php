<?php

namespace App\Clients;

use App\DTO\EmailDTO;
use App\Repositories\EmailLogRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Mailjet\Client;
use Mailjet\Resources;

class MailjetEmailClient implements EmailClientInterface
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
        $this->setClient(new Client(
            $key,
            $secret,
            $performer,
            [
                'version' => $version
            ]
        ));
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return self::CLIENT_NAME;
    }

    /**
     * @param  Client  $client
     * @return void
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param  EmailDTO  $emailDTO
     * @return bool
     */
    public function send(EmailDTO $emailDTO): bool
    {
        try {
            $response = $this->client->post(Resources::$Email, ['body' => [
                'Messages' => [
                    [
                        'From' => [
                            'Email' => $emailDTO->from['email'],
                            'Name' => $emailDTO->from['name'],
                        ],
                        'To' => [
                            [
                                'Email' => $emailDTO->to['email'],
                                'Name' => $emailDTO->to['name'],
                            ]
                        ],
                        'Subject' => $emailDTO->subject,
                        'HTMLPart' => $emailDTO->htmlPart,
                        'CustomID' => $emailDTO->id,
                    ]
                ]
            ]]);

            $this->emailLogRepository->update(
                $emailDTO->id,
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
