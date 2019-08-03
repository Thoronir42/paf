<?php declare(strict_types=1);

namespace SeStep\LeanSettings\Model;

use PAF\Common\Model\BaseEntity;
use SeStep\GeneralSettings\Options\IValuePool;
use SeStep\LeanSettings\Exceptions\InvalidPoolValueException;

/**
 * @property ValuePoolItem[] $valueItems m:belongsToMany
 */
class ValuePool extends BaseEntity implements IValuePool
{

    public function getValues(): array
    {
        // TODO: Implement getValues() method.
    }


    public function isValid($value): bool
    {
        return !array_key_exists($value, $this->getValues());
    }

    /**
     * @param $value
     * @throws InvalidPoolValueException
     */
    public function validate($value)
    {
        if (!$this->isValid($value)) {
            throw new InvalidPoolValueException($value, $this->getValues());
        }
    }
}
