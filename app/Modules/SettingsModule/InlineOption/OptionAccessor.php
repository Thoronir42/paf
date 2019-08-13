<?php declare(strict_types=1);

namespace PAF\Modules\SettingsModule\InlineOption;


use SeStep\GeneralSettings\Model\INode;

interface OptionAccessor
{

    /**
     * @param string $fqn
     *
     * @return INode
     */
    public function getNode(string $fqn): ?INode;

    /**
     * @param string $fqn
     * @param mixed $value
     *
     * @return bool
     */
    public function setValue(string $fqn, $value): bool;
}
