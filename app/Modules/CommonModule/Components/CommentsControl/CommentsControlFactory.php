<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Components\CommentsControl;

interface CommentsControlFactory
{
    public function create(): CommentsControl;
}
