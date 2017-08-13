<?php

namespace App\Common\Controls\Footer;


use Nette\Application\UI\Control;

class Footer extends Control
{
    public function render()
    {
        $this->template->setFile(__DIR__ . '/footer.latte');
        $this->template->render();
    }

    public function link($destination, $args = [])
    {
        return $this->presenter->link($destination, $args);
    }
}
