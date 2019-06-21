<?php


namespace SeStep\LeanSettings\Repository;


use PAF\Common\Model\BaseRepository;
use PAF\Common\Model\Exceptions\EntityNotFoundException;
use SeStep\LeanSettings\LeanOptionNode;
use SeStep\LeanSettings\LeanSection;

/**
 * Class LeanOptionNodeRepository
 * @package SeStep\LeanSettings\Repository
 *
 * @method LeanOptionNode|null findOneBy(array $criteria)
 * @method LeanOptionNode[] findBy(array $criteria)
 */
class LeanOptionNodeRepository extends BaseRepository
{
    public function find(string $name = null, string $domain = null): ?LeanOptionNode
    {
        return $this->findOneBy(['name' => $name, 'domain' => $domain]);
    }

    public function get(string $name = null, string $domain = null): LeanOptionNode
    {
        if ($result = $this->find($name, $domain)) {
            return $result;
        }

        throw new EntityNotFoundException();

    }

    public function getRootSection()
    {
        $section = $this->findSection(null, null);
        if (!$section) {
            $section = new LeanSection();
            $section->caption = 'Root entry of options';

            $this->insertIntoDatabase($section);
        }
    }

    public function findSection(string $name = null, string $domain = null): ?LeanSection
    {
        $result = $this->find($name, $domain);
        if ($result instanceof LeanSection) {
            return $result;
        }

        throw new EntityNotFoundException();
    }
}