<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Repository;

use PAF\Common\Model\BaseRepository;
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

    public function createSlug(string $string): Slug
    {
        $slug = new Slug();
        $slug->id = $string;

        $this->persist($slug);

        return $slug;
    }
}
