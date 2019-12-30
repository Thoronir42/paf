<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Model;

use LeanMapper\Entity;
use PAF\Modules\CommonModule\Model\Person;
use PAF\Modules\CommonModule\Model\Slug;
use PAF\Modules\CommonModule\Model\UserFileThread;

/**
 * @property string $id
 * @property Slug $slug m:hasOne(slug)
 * @property string $title
 * @property string|null $description
 * @property string $type m:enum(ProductType::TYPE_*)
 *
 * @property Commission|null $commission m:hasOne(commission_id)
 * @property Person $owner m:hasOne(owner_person_id)
 * @property UserFileThread $photos m:hasOne(photos_thread_id)
 */
class Product extends Entity
{
}
