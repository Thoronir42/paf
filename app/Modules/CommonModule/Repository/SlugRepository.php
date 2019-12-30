<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Repository;

use Nette\InvalidStateException;
use Nette\Utils\Strings;
use PAF\Common\Lean\BaseRepository;
use PAF\Modules\CommonModule\Model\Slug;

/**
 * Universally unique slug repository
 */
class SlugRepository extends BaseRepository
{

    public function slugExists(string $slug): bool
    {
        $result = $this->select()->where([
            'id' => $slug,
        ])->fetch();

        return !!$result;
    }

    public function createSlug(string $string, bool $numberSuffixIfExists = false): Slug
    {
        $slugId = Strings::trim(Strings::webalize($string), '-');

        if ($this->slugExists($slugId)) {
            if (!$numberSuffixIfExists) {
                throw new InvalidStateException('paf.commission.already-exists');
            }
            $sequence = $this->getMaxSlugSuffix($slugId) + 1;
            $slugId = "$slugId-$sequence";
        }
        $slug = new Slug();
        $slug->id = $slugId;

        $this->persist($slug);

        return $slug;
    }

    protected function getMaxSlugSuffix(string $slug): int
    {
        $maxSlug = $this->select('id')
            ->where('id LIKE %s', $slug . '%')
            ->orderBy('id DESC')
            ->limit(1)
            ->fetchSingle();

        $parts = explode('-', $maxSlug);
        $suffix = end($parts);
        if (!is_numeric($suffix)) {
            return 1;
        }

        return (int)$suffix;
    }
}
