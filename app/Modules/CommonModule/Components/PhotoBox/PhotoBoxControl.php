<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Components\PhotoBox;

use Nette\Application\UI;
use PAF\Modules\CommonModule\Model\UserFileThread;

class PhotoBoxControl extends UI\Control
{
    /** @var UserFileThread */
    private $thread;

    public function __construct(UserFileThread $thread)
    {
        $this->thread = $thread;
    }

    public function render()
    {
        $this->template->center = false;
        $this->template->thread = $this->thread;

        $this->template->render(__DIR__ . '/photoBox.latte');
    }
}
