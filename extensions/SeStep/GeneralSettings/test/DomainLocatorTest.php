<?php


namespace Test\SeStep\GeneralSettings;


use PHPUnit\Framework\TestCase;
use SeStep\GeneralSettings\DomainLocator;
use SeStep\GeneralSettings\Options\INode;

class DomainLocatorTest extends TestCase
{
    public function testConstruct()
    {
        $dl = new DomainLocator('prague');

        $this->assertEquals('prague', $dl->getName());
        $this->assertEquals('', $dl->getDomain());
        $this->assertEquals('prague', $dl->getFQN());


        $dl = new DomainLocator('prague', 'czechia');

        $this->assertEquals('prague', $dl->getName());
        $this->assertEquals('czechia', $dl->getDomain());
        $this->assertEquals('czechia.prague', $dl->getFQN());
    }

    public function testParentDomain()
    {
        $dl = new DomainLocator('prague', new CzechiaNode());

        $this->assertEquals('prague', $dl->getName());
        $this->assertEquals('earth.continents.europe.czechia', $dl->getDomain());
        $this->assertEquals('earth.continents.europe.czechia.prague', $dl->getFQN());
    }

    public function testDomainShifting()
    {
        $dl = new DomainLocator('a.bb.ccc.dddd');

        $this->assertEquals('a', $dl->shiftDomain());
        $this->assertEquals('bb.ccc', $dl->getDomain());
        $this->assertEquals('dddd', $dl->getName());

        $this->assertEquals('bb', $dl->shiftDomain());
        $this->assertEquals('ccc', $dl->getDomain());
        $this->assertEquals('dddd', $dl->getName());

        $this->assertEquals('ccc', $dl->shiftDomain());
        $this->assertEquals('', $dl->getDomain());
        $this->assertEquals('dddd', $dl->getName());

        $this->assertEquals('', $dl->shiftDomain());
        $this->assertEquals('', $dl->getDomain());
        $this->assertEquals('dddd', $dl->getName());

        // do nothing when domain is empty
        $this->assertEquals('', $dl->shiftDomain());
        $this->assertEquals('', $dl->getDomain());
        $this->assertEquals('dddd', $dl->getName());

    }

    public function testPop()
    {
        $dl = new DomainLocator("human.body.skeleton.fabella");

        $this->assertEquals('fabella', $dl->pop());
        $this->assertEquals('human.body', $dl->getDomain());
        $this->assertEquals('skeleton', $dl->getName());

        $this->assertEquals('skeleton', $dl->pop());
        $this->assertEquals('body', $dl->pop());

        $this->assertEquals('', $dl->getDomain());
        $this->assertEquals('human', $dl->getName());

        $this->assertEquals('human', $dl->pop());

        $this->assertEquals('', $dl->getFQN());
    }
}

class CzechiaNode implements INode
{

    /**
     * Returns fully qualified name. That is in most cases concatenated getDomain() and getName().
     * @return mixed
     */
    public function getFQN(): string
    {
        return 'earth.continents.europe.czechia';
    }

    public function getType(): string
    {
        return 'container';
    }

    public function getCaption(): string
    {
        return 'The silly country';
    }
}