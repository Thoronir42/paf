<?php declare(strict_types=1);

namespace PAF\Common\Security;

class AuthorizationResult
{
    const STATUS_OK = 0;
    const STATUS_FORBIDDEN = 1;

    /** @var int */
    private $status;
    /** @var string */
    private $message;
    /** @var array */
    private $args;

    public function __construct(int $status, string $message = null, array $args = [])
    {
        $this->status = $status;
        $this->message = $message;
        $this->args = $args;
    }

    public function isValid()
    {
        return $this->status == self::STATUS_OK;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getResource(): string
    {
        return $this->args['resource'];
    }

    public function getPrivilege(): ?string
    {
        return $this->args['privilege'] ?? null;
    }
}
