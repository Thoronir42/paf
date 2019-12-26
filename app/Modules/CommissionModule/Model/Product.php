<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Model;

use LeanMapper\Entity;
use Nette\Utils\DateTime;
use PAF\Modules\CommissionModule\Model\Specification;
use PAF\Modules\CommonModule\Model\Person;

/**
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property Specification|null m:hasOne(specification_id)
 * @property string $type m:enum(ProductType::TYPE_*)
 * @property Person $owner(owner_person_id)
 * @property DateTime $issuedOn
 * @property DateTime $completedOn
 */
class Product extends Entity
{
}
