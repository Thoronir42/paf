<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Model;

use LeanMapper\Entity;

/**
 * Class Person
 * @package PAF\Modules\CommonModule\Model
 *
 * @property string $id
 * @property string $displayName
 * @property User|null $user m:hasOne(user_id)
 *
 * @property Contact[] $contact m:belongsToMany(person_id)
 */
class Person extends Entity
{
    public function contactExists(Contact $contact): bool
    {
        foreach ($this->contact as $entry) {
            if ($contact->equals($entry)) {
                return true;
            }
        }

        return false;
    }
}
