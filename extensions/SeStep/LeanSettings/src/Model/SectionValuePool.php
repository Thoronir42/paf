<?php declare(strict_types=1);

namespace SeStep\LeanSettings\Model;

use SeStep\GeneralSettings\Model as GeneralModel;

class SectionValuePool implements GeneralModel\IValuePool
{
    private Section $section;

    public function __construct(Section $section)
    {
        $this->section = $section;
    }

    public function getValues(): array
    {
        return $this->sectionToValues($this->section);
    }

    public function isValueValid($value): bool
    {
        $nodes = $this->section->getNodes();
        return isset($nodes[$value]);
    }

    /**
     * @return Section
     * @internal
     */
    public function getSection(): Section
    {
        return $this->section;
    }

    private function sectionToValues(Section $section): array
    {
        $optionNodes = array_filter($section->getNodes(), function (GeneralModel\INode $node) {
            return $node instanceof GeneralModel\IOption;
        });

        return array_map(function (GeneralModel\IOption $option) {
            return $option->getValue();
        }, $optionNodes);
    }

    public function count()
    {
        return count($this->section);
    }
}
