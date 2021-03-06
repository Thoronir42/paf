<?php declare(strict_types=1);

namespace SeStep\LeanSettings\Repository;

use LeanMapper\Connection;
use LeanMapper\IEntityFactory;
use LeanMapper\IMapper;
use Nette\InvalidStateException;
use SeStep\LeanCommon\BaseRepository;
use SeStep\GeneralSettings\DomainLocator;
use SeStep\GeneralSettings\Exceptions\NodeNotFoundException;
use SeStep\GeneralSettings\Model\INode;
use SeStep\LeanSettings\Model\OptionNode;
use SeStep\LeanSettings\Model\Section;

/**
 * @method OptionNode|null findOneBy(array $criteria)
 * @method OptionNode[] findBy(array $criteria)
 */
class OptionNodeRepository extends BaseRepository
{
    public function get(string $name = null, string $domain = null): OptionNode
    {
        $fqn = DomainLocator::concatFQN($name, $domain);
        if ($result = $this->find($fqn)) {
            return $result;
        }

        throw new NodeNotFoundException($fqn);
    }

    public function getRootSection(): Section
    {
        $section = $this->findSection('.');
        if (!$section) {
            $section = new Section();
            $section->fqn = '';
            $section->caption = 'Root entry of options';

            $this->persist($section);
        }

        return $section;
    }

    public function getSection($fqn, bool $create = false): ?Section
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

        $parentNames = [];

        for ($dl = new DomainLocator($fqn); $dl->getFQN(); $dl->pop()) {
            array_unshift($parentNames, $dl->getFQN());
        }
        array_unshift($parentNames, '');

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

    public function deleteNode(string $fqn, bool $removeChildren = false)
    {
        $table = $this->getTable();

        $this->connection->begin();

        $rows = 0;
        if ($removeChildren) {
            $rows += $this->connection->delete($table)
                ->where('fqn LIKE ?', $fqn .'.%')
                ->execute();
        }

        $rows += $this->connection->delete($table)
            ->where('fqn = ?', $fqn)
            ->execute();

        $this->connection->commit();

        return $rows;
    }
}
