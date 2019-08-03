<?php

namespace SeStep\Commentable\Control;

interface CommentsControlFactory
{
    public function create(): CommentsControl;
}
