<?php declare(strict_types=1);

namespace PAF\Modules\OfferModule\Model\Entity;

use LeanMapper\Entity;
use PAF\Modules\DirectoryModule\Model\Person;

/**
 * @property string $id
 * @property string|null $slug
 * @property Person $supplier m:hasOne(supplier_person_id)
 *
 * @property string $type
 * @property string $name
 * @property string $description
 * @property string|null $previewImagePath
 * @property float|null $standardPrice
 *
 */
class Offer extends Entity
{
}
