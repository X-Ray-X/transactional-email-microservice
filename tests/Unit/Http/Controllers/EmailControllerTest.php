<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\EmailController;
use App\Http\Requests\SendEmailPost;
use App\Models\Email;
use Illuminate\Support\Facades\Validator;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class EmailControllerTest extends TestCase
{
    /**
     * @dataProvider \Tests\Unit\Http\Controllers\EmailControllerTest::sendProvider()
     * @param $requestProvider
     * @param $emailProvider
     * @param $responseProvider
     * @return void
     */
    public function testSend($requestProvider, $emailProvider, $responseProvider): void
    {
        $request = new SendEmailPost();
        $emailMock = Mockery::mock(Email::class);
        $controller = new EmailController();

        $request->setValidator(Validator::make($requestProvider, $request->rules()));

        $emailMock->expects('create')->once()->andReturn($emailMock);
        $emailMock->expects('send')->once()->andReturn($emailProvider);

        $response = $controller->send($request, $emailMock);

        $this->assertEquals(Response::HTTP_ACCEPTED, $response->getStatusCode());
        $this->assertEquals($responseProvider, $response->getOriginalContent());
    }

    /**
     * @dataProvider \Tests\Unit\Http\Controllers\EmailControllerTest::sendInvalidRequestProvider()
     *
     * @param $requestProvider
     * @param $errorProvider
     * @return void
     */
    public function testSendInvalidRequest($requestProvider, $errorProvider): void
    {
        $request = new SendEmailPost();
        $emailMock = Mockery::mock(Email::class);
        $controller = new EmailController();

        $request->setValidator(Validator::make($requestProvider, $request->rules()));

        try {
            $controller->send($request, $emailMock);
        } catch (\Exception $exception) {
            $this->assertInstanceOf(\Illuminate\Validation\ValidationException::class, $exception);
            $this->assertEquals($errorProvider, $exception->errors());
            $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $exception->status);
        }
    }

    /**
     * @return array[]
     */
    public function sendProvider(): array
    {
        return [
            [
                "request" => [
                    "from" => [
                        "email" => "john@doe.com",
                        "name" => "John Doe",
                    ],
                    "to"=>[
                        "email" => "jane@doe.com",
                        "name" => "Jane Doe"
                    ],
                    "subject" => "Test Email",
                    "htmlPart" => "<h1>This is a test email.</h1>"
                ],
                "email" => [
                    "id" => "decea8b6-25e5-4c7b-a765-a11b5a6fad4c",
                    "from" => [
                        "email" => "john@doe.com",
                        "name" => "John Doe",
                    ],
                    "to"=>[
                        "email" => "jane@doe.com",
                        "name" => "Jane Doe"
                    ],
                    "subject" => "Test Email",
                    "htmlPart" => "<h1>This is a test email.</h1>"
                ],
                "response" => [
                    "data" => [
                        "id" => "decea8b6-25e5-4c7b-a765-a11b5a6fad4c",
                        "from" => [
                            "email" => "john@doe.com",
                            "name" => "John Doe",
                        ],
                        "to"=>[
                            "email" => "jane@doe.com",
                            "name" => "Jane Doe"
                        ],
                        "subject" => "Test Email",
                        "htmlPart" => "<h1>This is a test email.</h1>"
                    ],
                ],
            ],
        ];
    }

    public function sendInvalidRequestProvider(): array
    {
        return [
            [
                "request" => [
                    "from" => [
                        "email" => " ",
                        "name" => " ",
                    ],
                    "to"=>[
                        "email" => " ",
                        "name" => " "
                    ],
                    "subject" => " ",
                    "htmlPart" => " "
                ],
                "errors" => [
                    "from.email" => ["The from.email field is required."],
                    "from.name" => ["The from.name field is required."],
                    "to.email" => ["The to.email field is required."],
                    "to.name" => ["The to.name field is required."],
                    "subject" => ["The subject field is required."],
                    "htmlPart" => ["The html part field is required."],
                ],
            ],
        ];
    }
}
