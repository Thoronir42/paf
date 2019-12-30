<?php declare(strict_types=1);

namespace PAF\Modules\DirectoryModule\Services;

class ContactDefinitions
{
    private $contactMethodSpecification;

    public function __construct(array $contactSpecification)
    {
        $this->contactMethodSpecification = $contactSpecification;
    }

    public function formatHref(string $contactMethod, $contactValue): string
    {
        if (!($specification = $this->getContactMethodSpecification($contactMethod))) {
            return '';
        }

        return sprintf($specification['format'], $contactValue);
    }

    public function getIconClass(string $contactMethod): string
    {
        if (!($specification = $this->getContactMethodSpecification($contactMethod))) {
            return '';
        }

        return $specification['icon'] ?? '';
    }

    private function getContactMethodSpecification(string $type): ?array
    {
        $specification = $this->contactMethodSpecification[$type] ?? null;
        if (!$specification) {
            trigger_error("Contact type '{$type}' not specified");
            return null;
        }

        return $specification;
    }
}
