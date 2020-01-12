<?php declare(strict_types=1);

namespace PAF\Modules\CmsModule\Model;

use LeanMapper\Entity;

/**
 * @property int $id
 * @property string $slug
 * @property string $content
 */
class Page extends Entity
{
    protected function initDefaults()
    {
        $this->content = '';
    }
}
