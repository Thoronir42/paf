<?php declare(strict_types=1);

namespace PAF\Modules\DirectoryModule\Repository;

use Dibi\Expression;
use PAF\Common\Lean\BaseRepository;
use PAF\Modules\DirectoryModule\Model\Contact;
use PAF\Modules\DirectoryModule\Model\Person;

class PersonRepository extends BaseRepository
{
    /**
     * @param Contact[] $contacts
     * @return Person|null
     */
    public function findByContact(array $contacts): ?Person
    {
        $query = $this->select('p.*', 'p')
            ->join($this->mapper->getTable(Contact::class) . ' AS c')
            ->on('c.person_id = p.id')
            ->groupBy('p.id');


        $clauses = [];
        foreach ($contacts as $contact) {
            $clauses[] = new Expression("c.type = ? AND c.value = ?", $contact->type, $contact->value);
        }

        $query->where('%or', $clauses);

        $result = $query->fetch();
        if (!$result) {
            return null;
        }

        return $this->createEntity($result);
    }
}
