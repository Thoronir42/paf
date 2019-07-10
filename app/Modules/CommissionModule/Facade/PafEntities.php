<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Facade;


use Nette\Utils\Strings;
use PAF\Modules\CommissionModule\Model\PafCase;
use PAF\Modules\CommissionModule\Repository\PafCaseRepository;
use PAF\Modules\QuoteModule\Model\Quote;
use PAF\Modules\QuoteModule\Repository\QuoteRepository;

class PafEntities
{
    /** @var QuoteRepository */
    private $quotes;

    /** @var PafCaseRepository */
    private $cases;

    public function __construct(QuoteRepository $quotes, PafCaseRepository $cases)
    {
        $this->quotes = $quotes;
        $this->cases = $cases;
    }

    public function createQuote(Quote $quote)
    {
        $slug = self::sluggify($quote->getFursuit()->getName());
        if ($this->entityExists($slug)) {
            return false;
        }

        $this->save($quote, false);

        return true;
    }


    /**
     * @param string    $name
     * @param null|bool $softDeleted
     *
     * @return bool
     */
    public function entityExists($name, $softDeleted = false)
    {
        $where = ['name' => $name];
        if (is_bool($softDeleted)) {
            $where['deleted'] = $softDeleted;
        }

        return $this->repository->countBy($where) > 0;
    }

    /**
     * @param $name
     * @return PafWrapper|null
     */
    public function findByName($name) {
        return $this->repository->findOneBy(['name' => $name]);
    }

    /**
     * @param Quote $quote
     * @return string
     */
    public function acceptQuote(Quote $quote)
    {
        $wrapper = $quote->getWrapper();

        $quote->setStatus(Quote::STATUS_ACCEPTED);
        $this->quotes->save($quote, false);

        if($wrapper->getCase()) {
            $this->flushAll();
            return 'already-exists';
        }

        $case = new PafCase($quote->getContact(), $quote->getFursuit());
        $wrapper->setCase($case);

        $this->cases->save($case, false);
        $this->save($wrapper);
    }

    public function rejectQuote(Quote $quote)
    {
        $wrapper = $quote->getWrapper();

        $quote->setStatus(Quote::STATUS_REJECTED);
        $wrapper->setDeleted(true);

        $this->quotes->save($quote, false);
        $this->save($wrapper);

    }

    protected static function sluggify($name) {
        return Strings::webalize($name);
    }
}