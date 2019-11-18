<?php declare(strict_types=1);

namespace SeStep\EntityIds;

use Nette\Utils\Random;

final class TextIdGenerator implements IdGenerator
{

    /** @var CharSet */
    private $charSet;
    /** @var int */
    private $length;

    /**
     * TextIdGenerator constructor.
     *
     * @param CharSet $charSet
     * @param int $length
     */
    public function __construct(CharSet $charSet, int $length)
    {
        $this->charSet = $charSet;
        $this->length = $length;
    }


    /**
     * Tests whether generator provides IDs for given type
     *
     * @param string $type
     * @return bool
     */
    public function hasType(string $type): bool
    {
        return false;
    }

    /**
     * Creates new ID of given type
     *
     * @param string|null $type
     * @return string
     */
    public function generateId(string $type = null): string
    {
        return Random::generate($this->length, $this->charSet->getChars());
    }

    /**
     * If possible, retrieves the type of given id
     *
     * @param string $id
     * @return string|null
     */
    public function getType(string $id): ?string
    {
        return null;
    }
}
