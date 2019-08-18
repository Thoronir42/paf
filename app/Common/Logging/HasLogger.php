<?php declare(strict_types=1);

namespace PAF\Common\Logging;

use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Trait HasLogger
 * @package PAF\Common
 *
 * To use with {@link Psr\Log\LoggerAwareInterface}
 */
trait HasLogger
{
    /** @var LoggerInterface */
    private $logger;

    protected function getLogger(): LoggerInterface
    {
        if (!$this->logger) {
            $this->logger = new Logger('null');
        }

        return $this->logger;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
