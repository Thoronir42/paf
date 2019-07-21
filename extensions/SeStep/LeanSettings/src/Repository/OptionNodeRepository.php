<?php


namespace SeStep\LeanSettings\Repository;


use Dibi\NotImplementedException;
use Nette\InvalidStateException;
use PAF\Common\Model\BaseRepository;
use PAF\Common\Model\Exceptions\EntityNotFoundException;
use SeStep\GeneralSettings\DomainLocator;
use SeStep\GeneralSettings\Options\INode;
use SeStep\LeanSettings\Model\OptionNode;
use SeStep\LeanSettings\Model\Section;

/**
 * Class LeanOptionNodeRepository
 * @package SeStep\LeanSettings\Repository
 *
 * @method OptionNode|null findOneBy(array $criteria)
 * @method OptionNode[] findBy(array $criteria)
 */
class OptionNodeRepository extends BaseRepository
{
    public function find(string $fqn): ?OptionNode
    {
        if (!$fqn) {
            $fqn = '.';
        }

        if ($fqn[0] !== '.') {
            $term = '%' . $fqn;
        } else {
            $term = $fqn;
        }

        return $this->findOneBy(['fqn' => $term]);
    }

    public function get(string $name = null, string $domain = null): OptionNode
    {
        if ($result = $this->find($name, $domain)) {
            return $result;
        }

        throw new EntityNotFoundException();

    }

    public function getRootSection(): Section
    {
        $section = $this->findSection('.');
        if (!$section) {
            $section = new Section();
            $section->fqn = '.';
            $section->caption = 'Root entry of options';

            $this->persist($section);
        }

        return $section;
    }

    function getSection($fqn, bool $create = false): ?Section
    {
        $section = $this->find($fqn);
        if (!$section) {
            return $create ? $this->createSection($fqn) : null;
        } else {
            if (!$section instanceof Section) {
                $err = "Option node '$fqn' is not instance of Section, got: " . get_class($section);
                throw new InvalidStateException($err);
            }
        }


        return $section;
    }

    public function createSection(string $fqn, string $caption = null): Section
    {
        if ($this->find($fqn)) {
            throw new InvalidStateException("Section '$fqn' already exists");
        }

        $dl = new DomainLocator($fqn);

        $parentNames = [];

        for (; $dl->getFQN(); $dl->pop()) {
            array_unshift($parentNames, $dl->getFQN());
        }

        $rootSection = $this->getSection(INode::DOMAIN_DELIMITER);
        $section = $this->resolveSectionChain($parentNames, $rootSection);
        $section->caption = $caption;


        $this->persist($section);

        return $section;
    }

    public function findSection(string $fqn): ?Section
    {
        $result = $this->find($fqn);

        if (!$result) {
            return null;
        }

        if ($result instanceof Section) {
            return $result;
        }

        throw new InvalidStateException("Option node '$fqn' is not a section");
    }

    private function resolveSectionChain(array $names, Section $parent = null)
    {
        $sections = $this->findSections($names);

        foreach ($names as $name) {
            if (!isset($sections[$name])) {
                $section = new Section();
                $section->fqn = $name;
                if ($parent) {
                    $section->parentSection = $parent;
                }

                $this->persist($section);
                $sections[$name] = $section;
            }

            $parent = $sections[$name];
        }

        return $parent;
    }

    /**
     * @param string[] $parentNames
     *
     * @return Section[]
     */
    public function findSections(array $parentNames): array
    {
        $selection = $this->select()
            ->where('fqn IN %in', $parentNames)
            ->fetchAssoc('fqn');

        return array_map([$this, 'createEntity'], $selection);

    }


}