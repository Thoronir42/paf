<?php declare(strict_types=1);

namespace SeStep\LeanSettings\Model;


use PAF\Common\Model\BaseEntity;
use SeStep\GeneralSettings\Options\INode;
use SeStep\LeanSettings\Model\Option;

/**
 * @property int $id
 * @property string $fqn
 * @property string|null $caption
 *
 * @property string $type
 *
 * @property Section $parentSection m:hasOne(parent_section_id)
 *
 */
abstract class OptionNode extends BaseEntity implements INode
{

    /**
     * Returns fully qualified name. That is in most cases concatenated getDomain() and getName().
     * @return mixed
     */
    public function getFQN(): string
    {
        return $this->row->fqn;
    }

    public function getCaption(): ?string
    {
        return $this->row->caption;
    }
}
