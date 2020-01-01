<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Services;

use Nette\InvalidStateException;

class DashboardService
{
    private $stats;

    public function __construct(array $stats = [])
    {
        $this->stats = $stats;
    }

    public function registerStat(string $name, $stat)
    {
        if (isset($this->stats[$name])) {
            throw new InvalidStateException("Stat '$name' already exists");
        }

        $this->stats[$name] = $stat;
    }

    public function getStats()
    {
        $values = [];

        foreach ($this->stats as $name => $value) {
            if (is_callable($value)) {
                $value = call_user_func($value);
            }

            $values[$name] = $value;
        }

        return $values;
    }
}
