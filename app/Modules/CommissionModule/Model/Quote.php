<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Model;

use DateTime;
use LeanMapper\Entity;
use PAF\Modules\DirectoryModule\Model\Person;
use PAF\Modules\CommonModule\Model\Slug;
use PAF\Modules\CommonModule\Model\UserFileThread;

/**
 * @property string $id
 * @property Person $supplier m:hasOne(supplier_person_id)
 * @property Slug $slug m:hasOne(slug)
 * @property string $status m:enum(self::STATUS*)
 * @property DateTime $dateCreated
 *
 * @property Person $issuer m:hasOne(issuer_person_id)
 * @property Specification $specification m:hasOne(specification_id)
 */
class Quote extends Entity
{
    const STATUS_NEW = 'new';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

    protected function initDefaults()
    {
        $this->dateCreated = new DateTime();
    }


    public static function getStatuses()
    {
        return [
            self::STATUS_NEW,
            self::STATUS_ACCEPTED,
            self::STATUS_REJECTED,
        ];
    }

    public function hasReferences(): bool
    {
        $prop = $this->getCurrentReflection()->getEntityProperty('references');
        $column = $prop->getColumn();
        $rowData = $this->getRowData();

        return array_key_exists($column, $rowData) && $rowData[$column];
    }
}
