<?php


namespace SeStep\LeanSettings\Repository;


use Nette\InvalidStateException;
use PAF\Common\Model\BaseRepository;
use PAF\Common\Model\Exceptions\EntityNotFoundException;
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