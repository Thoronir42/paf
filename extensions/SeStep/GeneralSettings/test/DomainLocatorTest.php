<?php


namespace Test\SeStep\GeneralSettings;


use PHPUnit\Framework\TestCase;
use SeStep\GeneralSettings\DomainLocator;
use SeStep\GeneralSettings\Options\INode;

class DomainLocatorTest extends TestCase
{
    public function testNoDomain()
    {
        $dl = DomainLocator::create('prague');

        $this->assertEquals('prague', $dl->getName());
        $this->assertEquals('', $dl->getDomain());
        $this->assertEquals('prague', $dl->getFQN());
    }

    public function testStringDomain()
    {
        $dl = DomainLocator::create('prague', 'czechia');

        $this->assertEquals('prague', $dl->getName());
        $this->assertEquals('czechia', $dl->getDomain());
        $this->assertEquals('czechia.prague', $dl->getFQN());
    }

    public function testParentDomain()
    {
        $dl = DomainLocator::create('prague', new CzechiaNode());

        $this->assertEquals('prague', $dl->getName());
        $this->assertEquals('earth.continents.europe.czechia', $dl->getDomain());
        $this->assertEquals('earth.continents.europe.czechia.prague', $dl->getFQN());
    }

    public function testDomainShifting()
    {
        $dl = DomainLocator::create('a.bb.ccc.dddd');

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
