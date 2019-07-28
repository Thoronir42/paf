<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Model;

use PAF\Common\Model\BaseEntity;

/**
 * @property int $id
 * @property User $user m:hasOne(user_id)
 * @property string $type m:enum(Contact::TYPE_*)
 * @property string $value
 */
class Contact extends BaseEntity
{
    const TYPE_EMAIL = 'email';
    const TYPE_TELEGRAM = 'telegram';
    const TYPE_TELEPHONE = 'telephone';
    const TYPE_OTHER = 'other';
}
