<?php


namespace SeStep\LeanSettings;


use PAF\Common\Model\BaseEntity;
use SeStep\GeneralSettings\Options\INode;

/**
 * @property string $domain
 * @property string $name
 * @property string $caption
 *
 * @property LeanOptionNode $parentNode m:belongsToOne
 *
 */
abstract class LeanOptionNode extends BaseEntity implements INode
{

    /**
     * Returns fully qualified name. That is in most cases concatenated getDomain() and getName().
     * @return mixed
     */
    public function getFQN(): string
    {
        $rowData = $this->getRowData();
        return $rowData['domain'] . self::DOMAIN_DELIMITER . $rowData['name'];
    }

    public function getCaption(): string
    {
        return $this->getRowData()['caption'];
    }
}