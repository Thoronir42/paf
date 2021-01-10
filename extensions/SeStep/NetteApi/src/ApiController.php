<?php declare(strict_types=1);

namespace SeStep\NetteApi;

use Nette\Application\BadRequestException;
use Nette\Application\Responses\JsonResponse;
use Nette\Http\IResponse;
use Nette\Http\Request as HttpRequest;
use Nette\Utils\JsonException;
use SeStep\NetteApi\JwtService;

trait ApiController
{
    /** @inject */
    public HttpRequest $httpRequest;

    /** @inject  */
    public JwtService $jwtService;

    protected function parseRequestBodyJson(): object
    {
        $body = $this->httpRequest->getRawBody();

        try {
            return \Nette\Utils\Json::decode($body);
        } catch (JsonException $ex) {
            throw new \Nette\Application\BadRequestException("Request body is not a JSON");
        }
    }

    protected function response($payload): JsonResponse
    {
        return new JsonResponse($payload);
    }
}
