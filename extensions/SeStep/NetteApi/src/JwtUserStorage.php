<?php declare(strict_types=1);

namespace SeStep\NetteApi;

use Nette\Application\BadRequestException;
use Nette\Http\IResponse;
use Nette\Http\Request;
use Nette\InvalidStateException;
use Nette\NotSupportedException;
use Nette\Security\IIdentity;
use Nette\Security\IUserStorage;
use SeStep\NetteApi\JwtService;
use PAF\Common\Security\LiveUserIdentity;
use PAF\Modules\CommonModule\Services\Users;

class JwtUserStorage implements IUserStorage
{
    private JwtService $jwtService;
    private Request $request;

    private ?object $tokenData = null;
    private Users $users;

    public function __construct(JwtService $jwtService, Users $users, Request $request)
    {
        $this->jwtService = $jwtService;
        $this->request = $request;
        $this->users = $users;
    }


    public function isAuthenticated(): bool
    {
        return !!$this->readAuthorization();
    }

    public function getIdentity(): ?IIdentity
    {
        $data = $this->readAuthorization();
        $identity = new LiveUserIdentity($data->id);
        $user = $this->users->findUserById($identity->getId());
        if (!$user) {
            throw new InvalidStateException("Logged in user could not be found");
        }
        $roles = $this->users->getRoles($user);

        $identity->initialize($user, $roles);
        return $identity;
    }

    public function setAuthenticated(bool $state)
    {
        throw new NotSupportedException("Jwt user storage is only for retrieving");
    }

    public function setIdentity(?IIdentity $identity)
    {
        throw new NotSupportedException("Jwt user storage is only for retrieving");
    }

    public function setExpiration(?string $expire, int $flags = 0)
    {
        throw new NotSupportedException("Jwt user storage is only for retrieving");
    }

    public function getLogoutReason(): ?int
    {
        return -1;
    }

    private function readAuthorization(): object
    {
        if ($this->tokenData) {
            return $this->tokenData;
        }

        $header = $this->request->getHeader('Authorization');
        if (!$header) {
            throw new BadRequestException('requestError.missingAuthorization', IResponse::S401_UNAUTHORIZED);
        }
        [$type, $token] = explode(" ", $header);
        if (strtolower($type) !== "bearer") {
            throw new BadRequestException("requestError.invalidAuthorization", IResponse::S401_UNAUTHORIZED);
        }
        $data = $this->jwtService->decode($token);
        return $this->tokenData = $data;
    }
}
