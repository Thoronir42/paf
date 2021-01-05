<?php declare(strict_types=1);

namespace PAF\Common\Lean;

use Nette\Application\UI\Form;
use PAF\Common\Forms\FormCustomControls;

class GenericEntityForm extends Form
{
    use EntityHandling;
    use FormCustomControls;
}
