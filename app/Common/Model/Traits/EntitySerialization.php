<?php

namespace App\Common\Model\Traits;


use Doctrine\Common\Collections\Collection;
use SeStep\Model\BaseEntity;

trait EntitySerialization
{
    public function toArray()
    {
        $array = [];
        foreach ($this as $field => $value) {
            if ($value instanceof BaseEntity) {
                $value = $value->getId();
            } elseif ($value instanceof Collection) {
                $value = array_map(function(BaseEntity $entity) {
                    return $entity->getId();
                }, $value->toArray());
            } elseif (gettype($value) === 'object' && in_array(EntitySerialization::class,
                    class_uses(get_class($value)))) {
                /** @var EntitySerialization $value */
                $value = $value->toArray();
            }
            $array[$field] = $value;
        }

        return $array;
    }
}
