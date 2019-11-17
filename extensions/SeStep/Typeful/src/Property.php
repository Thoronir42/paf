<?php declare(strict_types=1);

namespace SeStep\Typeful;

class Property
{
    /** @var string */
    private $name;
    /** @var string */
    private $type;

    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
