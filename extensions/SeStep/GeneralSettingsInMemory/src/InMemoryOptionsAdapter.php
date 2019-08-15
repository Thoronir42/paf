<?php declare(strict_types=1);

namespace SeStep\GeneralSettingsInMemory;

use Nette\InvalidStateException;
use SeStep\GeneralSettings\Exceptions\ValuePoolAlreadyExistsException;
use SeStep\GeneralSettings\IOptionsAdapter;
use SeStep\GeneralSettings\IValuePoolsAdapter;
use SeStep\GeneralSettings\Model as GeneralModel;
use SeStep\GeneralSettings\Model\IValuePool;
use SeStep\GeneralSettingsInMemory\Model\InMemoryOption;
use SeStep\GeneralSettingsInMemory\Model\InMemoryValuePool;

final class InMemoryOptionsAdapter extends Model\InMemoryOptionSection implements IOptionsAdapter, IValuePoolsAdapter
{

    private $rootData;

    private $pools;

    public function __construct()
    {
        $this->rootData = [];
        parent::__construct($this, '', $this->rootData);
    }


    public function getFQN(): string
    {
        return '';
    }

    public function getPool(string $name): ?IValuePool
    {
        return $this->pools[$name] ?? null;
    }

    /** @inheritDoc */
    public function createPool(string $name, array $values): GeneralModel\IValuePool
    {
        if (isset($this->pools[$name])) {
            throw new ValuePoolAlreadyExistsException("Value pool $name already exists");
        }

        $pool = new InMemoryValuePool($name, $values);

        return $this->pools[$name] = $pool;
    }

    /** @inheritDoc */
    public function updateValues(GeneralModel\IValuePool $pool, array $values)
    {
        if (!$pool instanceof InMemoryValuePool) {
            throw new \InvalidArgumentException();
        }
        if (empty($values)) {
            throw new \InvalidArgumentException("Values array must not be empty");
        }

        $pool->setValues($values);
        return true;
    }

    /** @inheritDoc */
    public function deletePool($pool): bool
    {
        if (is_string($pool)) {
            if (!isset($this->pools[$pool])) {
                return false;
            }
            unset($this->pools[$pool]);
            return true;
        }
        if ($pool instanceof InMemoryValuePool) {
            foreach ($this->pools as $name => $currentPool) {
                if ($pool == $currentPool) {
                    unset($this->pools[$name]);
                    return true;
                }
            }

            return false;
        }

        throw new \InvalidArgumentException();
    }

    /** @inheritDoc */
    public function setOptionsPool(GeneralModel\IOption $option, ?GeneralModel\IValuePool $pool)
    {
        if (!$option instanceof InMemoryOption) {
            throw new \InvalidArgumentException();
        }

        if ($pool) {
            if (!$pool instanceof InMemoryValuePool) {
                throw new \InvalidArgumentException();
            }
            $poolName = $pool->getName();
            if (!isset($this->pools[$poolName])) {
                throw new InvalidStateException("Pool $poolName does not exist");
            }
            $option->setValuePool($poolName);
        } else {
            $option->setValuePool(null);
        }

        return true;
    }
}
