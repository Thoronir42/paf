<?php declare(strict_types=1);

namespace SeStep\Commentable\Control;

interface CommentsControlFactory
{
    public function create(): CommentsControl;
}
