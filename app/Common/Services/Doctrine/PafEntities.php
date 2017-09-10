<?php

namespace App\Common\Services\Doctrine;


use App\Common\Model\Entity\PafCase;
use App\Common\Model\Entity\PafWrapper;
use App\Common\Model\Entity\Quote;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\Strings;
use SeStep\Model\BaseDoctrineService;

class PafEntities extends BaseDoctrineService
{
    /** @var Quotes */
    private $quotes;

    /** @var PafCases */
    private $cases;

    public function __construct($class, EntityManager $em, Quotes $quotes, PafCases $cases)
    {
        parent::__construct($class, $em);
        $this->quotes = $quotes;
        $this->cases = $cases;
    }

    public function createQuote(Quote $quote)
    {
        $slug = self::sluggify($quote->getFursuit()->getName());
        if ($this->entityExists($slug)) {
            return false;
        }

        $wrapper = new PafWrapper($slug);
        $wrapper->setQuote($quote);

        $this->save($quote, false);
        $this->save($wrapper, false);

        $this->flushAll();

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
