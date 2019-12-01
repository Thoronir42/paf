<?php declare(strict_types=1);

namespace SeStep\Typeful;

class TypeRegister
{
    /** @var PropertyType[] */
    private $propertyTypes;

    /**
     * TypeRegister constructor.
     *
     * @param PropertyType[] $propertyTypes
     */
    public function __construct(array $propertyTypes)
    {
        foreach ($propertyTypes as $type) {
            $this->propertyTypes[$type::getName()] = $type;
        }
    }

    public function getPropertyType(string $type): ?PropertyType
    {
        if (!isset($this->propertyTypes[$type])) {
            trigger_error("Property type '$type' is not recognized");
            return null;
        }

        return $this->propertyTypes[$type];
    }
}
