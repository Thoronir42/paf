<?php declare(strict_types=1);
namespace SeStep\LeanSettings;

use LeanMapper\Row;
use SeStep\GeneralSettings\Options\IOptionSection;
use SeStep\LeanSettings\Model\Option;
use SeStep\LeanSettings\Model\OptionNode;
use SeStep\LeanSettings\Model\Section;
use SeStep\ModularLeanMapper\MapperModule;

class LeanOptionsMapperModule extends MapperModule
{
    public function __construct()
    {
        parent::__construct(__NAMESPACE__ . '\Model', __NAMESPACE__ . '\Repository');
    }

    public function getEntityClass(string $table, Row $row = null): ?string
    {
        if ($table == 'ss_settings__option_node') {
            if (!$row) {
                return OptionNode::class;
            }

            if ($row->type == IOptionSection::TYPE_SECTION) {
                return Section::class;
            }

            return Option::class;
        }

        return null;
    }
}
