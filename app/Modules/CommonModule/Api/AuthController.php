<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Api;

use Nette\Application\BadRequestException;
use Nette\Application\IPresenter;
use Nette\Application\IResponse;
use Nette\Application\Request;
use Nette\Http\IResponse as HttpResponse;
use Nette\Security\AuthenticationException;
use PAF\Common\Security\Authenticator;
use SeStep\Moment\HasMomentProvider;
use SeStep\NetteApi\ApiController;

class AuthController implements IPresenter
{
    use ApiController;
    use HasMomentProvider;

    /** @inject */
    public Authenticator $authenticator;

    public function run(Request $request): IResponse
    {
        if ($request->method !== 'POST') {
            throw new BadRequestException('requestError.methodNotAllowed', HttpResponse::S405_METHOD_NOT_ALLOWED);
        }
        $body = $this->parseRequestBodyJson();

        try {
            $expireAt = $this->getMomentProvider()->now()->modify('+ 1 day');
            $jwt = $this->authenticator->authenticateToken([$body->login, $body->password], $expireAt);
        } catch (AuthenticationException $ex) {
            throw new BadRequestException('authError.credentialsNotRecognized', 401);
        }

        return $this->response([
            'token' => $jwt,
        ]);
    }
}
