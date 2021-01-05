<?php declare(strict_types=1);

namespace PAF\Common\Model;

use Dibi\Exception;
use Dibi\Connection;
use PAF\Common\Logging\HasLogger;
use PAF\Common\Model\Exceptions\TransactionFailedException;

/**
 * Transaction service providing atomic wrapper for callbacks
 */
class TransactionManager
{
    use HasLogger;

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Executes callback atomically or performs a rollback if exception occurs
     *
     * @param callable $callback
     * @param mixed ...$arguments
     *
     * @return mixed
     * @throws TransactionFailedException
     */
    public function execute(callable $callback, ...$arguments)
    {
        try {
            $this->connection->begin();
            $result = call_user_func_array($callback, $arguments);
            $this->connection->commit();

            return $result;
        } catch (\Exception $exception) {
            try {
                $this->connection->rollback();
            } catch (Exception $ex) {
                $this->getLogger()->error("Rollback failed");
            }

            throw new TransactionFailedException("Transaction failed: " . $exception->getMessage(), $exception);
        }
    }
}
