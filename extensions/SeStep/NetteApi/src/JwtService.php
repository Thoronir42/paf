<?php declare(strict_types=1);

namespace SeStep\NetteApi;

use Firebase\JWT\JWT;

class JwtService
{
    private string $key;

    private string $algo = 'HS256';

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /** @param string $algo */
    public function setAlgo(string $algo): void
    {
        $this->algo = $algo;
    }

    /**
     * @param array|\stdClass $data
     * @return string
     */
    public function encode($data): string
    {
        return JWT::encode($data, $this->key, $this->algo);
    }

    /**
     * @param string $jwt
     * @return object
     */
    public function decode(string $jwt): object
    {
        return JWT::decode($jwt, $this->key, [$this->algo]);
    }
}
