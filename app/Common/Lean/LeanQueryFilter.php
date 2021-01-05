<?php declare(strict_types=1);

namespace PAF\Common\Lean;

use Dibi\Expression;
use Dibi\Fluent;
use LeanMapper\Entity;
use LeanMapper\Exception\InvalidArgumentException;
use LeanMapper\IMapper;

class LeanQueryFilter
{
    /** @var IMapper */
    private IMapper $mapper;

    public function __construct(IMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function apply(Fluent $fluent, array $conditions, string $entityClass)
    {
        if (!is_a($entityClass, Entity::class, true)) {
            throw new \InvalidArgumentException("$entityClass is not an entity class");
        }

        foreach ($conditions as $property => $condition) {
            if ($not = $property[0] === '!') {
                $property = substr($property, 1);
            }
            $expression = $this->createExpression($entityClass, $property, $condition, !$not);
            $fluent->where($expression);
        }
    }

    /**
     * @param Fluent $fluent
     * @param array $order
     * @param string|Entity $entityClass
     * @throws InvalidArgumentException
     */
    public function order(Fluent $fluent, array $order, string $entityClass)
    {
        if (empty($order)) {
            return;
        }

        $reflection = $entityClass::getReflection($this->mapper);

        foreach ($order as $property => $direction) {
            if (is_int($property)) {
                $property = $direction;
                $direction = 'ASC';
            }

            $propertyReflection = $reflection->getEntityProperty($property);
            if (!$propertyReflection) {
                throw new InvalidArgumentException("Property '$property' does not exist");
            }
            if ($propertyReflection->hasRelationship()) {
                throw new InvalidArgumentException("Property '$property' has a relationship and cannot be" .
                    " used for ordering");
            }

            $column = $propertyReflection->getColumn();
            $fluent->orderBy("$column $direction");
        }
    }

    /**
     * @param string|Entity $entityClass
     * @param string $property
     * @param mixed $value
     * @param bool $fulfilled
     *
     * @return Expression
     * @throws InvalidArgumentException
     */
    public function createExpression($entityClass, string $property, $value, bool $fulfilled = true): Expression
    {
        $reflection = $entityClass::getReflection($this->mapper);
        $propertyRef = $reflection->getEntityProperty($property);
        if (!$propertyRef) {
            throw new InvalidArgumentException("Property '$property' does not exist");
        }
        $column = $propertyRef->getColumn();
        $expression = $this->makeColumnExpression($column, $value);
        if (!$fulfilled) {
            $expression = new Expression('NOT(?)', $expression);
        }

        return $expression;
    }

    private function makeColumnExpression(string $column, $value): Expression
    {
        if ($value instanceof Expression) {
            return new Expression($column, $value);
        }
        $value = $this->normalizeValue($value);

        if (is_array($value)) {
            return new Expression("$column IN %in", $value);
        } elseif (is_null($value)) {
            return new Expression("$column IS NULL");
        } elseif (is_string($value) && strpos($value, '%') !== false) {
            return new Expression("$column LIKE ?", $value);
        } else {
            return new Expression("$column = %s", $value);
        }
    }

    private function normalizeValue($value)
    {
        if (is_array($value)) {
            $result = [];
            foreach ($value as $key => $item) {
                $result[$key] = $this->normalizeValue($item);
            }

            return $result;
        }
        if ($value instanceof Entity) {
            return $value->id;
        }

        return $value;
    }
}
