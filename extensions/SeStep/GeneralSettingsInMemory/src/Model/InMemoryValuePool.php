<?php declare(strict_types=1);

namespace SeStep\GeneralSettingsInMemory\Model;

use SeStep\GeneralSettings\Model\IValuePool;

class InMemoryValuePool implements IValuePool
{
    /** @var string */
    private $name;
    /** @var array */
    private $values;

    public function __construct(string $name, array $values)
    {
        $this->name = $name;
        $this->values = $values;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param array $values
     * @internal
     */
    public function setValues(array $values)
    {
        $this->values = $values;
    }

    /** @return string */
    public function getName(): string
    {
        return $this->name;
    }

    public function isValueValid($value): bool
    {
        return isset($this->values[$value]);
    }
}
