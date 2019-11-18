<?php declare(strict_types=1);

namespace SeStep\Typeful;

interface EntityDescriptor
{
    public function getProperty(string $name): ?Property;

    public function getPropertyFullName(string $property): ?string;
}
