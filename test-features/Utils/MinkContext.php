<?php declare(strict_types=1);

namespace Behat\PAF\Utils;

use Behat\Mink\Mink;

trait MinkContext
{
    /** @var Mink */
    private $mink;
    /** @var array */
    private $minkParameters;

    public function setMink(Mink $mink)
    {
        $this->mink = $mink;
    }

    public function setMinkParameters(array $parameters)
    {
        $this->minkParameters = $parameters;
    }

    private function getMinkSession()
    {
        return $this->mink->getSession();
    }

    private function getPage()
    {
        return $this->mink->getSession()->getPage();
    }

    private function visit(string $url)
    {
        $this->mink->getSession()->visit($this->minkParameters['base_url'] . $url);
    }
}
