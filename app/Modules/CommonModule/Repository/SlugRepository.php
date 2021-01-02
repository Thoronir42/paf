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
        $existingSlugs = $this->select('COUNT(id)')->where(['id' => $slug])->fetchSingle();
        return $existingSlugs > 0;
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
        $maxSlug = $this->select('CAST(SUBSTRING(id, LOCATE("-", id) + 1, CHAR_LENGTH(id)) AS int) as seqNum')
            ->where('id LIKE %s', $slug . '-%')
            ->orderBy('seqNum DESC')
            ->limit(1)->offset(0)
            ->fetchSingle();

        return is_numeric($maxSlug) ? $maxSlug : 1;
    }
}
