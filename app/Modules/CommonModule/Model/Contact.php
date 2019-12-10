<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Model;

use LeanMapper\Entity;

/**
 * @property int $id
 * @property Person $person m:hasOne(person_id)
 * @property string|null $type m:enum(Contact::TYPE_*)
 * @property string $value
 */
class Contact extends Entity
{
    const TYPE_EMAIL = 'email';
    const TYPE_TELEGRAM = 'telegram';
    const TYPE_TELEPHONE = 'telephone';

    protected function initDefaults()
    {
        $this->type = null;
        $this->value = '';
    }

    public function equals($other): bool
    {
        if (!$other instanceof Contact) {
            return false;
        }

        return $other->type == $this->type && $other->value == $this->value;
    }

    public function isEmpty()
    {
        return !$this->value;
    }
}
