<?php declare(strict_types=1);

namespace SeStep\NetteBootstrap;

use Nette\Utils\Html;

class InputGroup extends Html
{
    public function __construct(Html $input)
    {
        $this->setName('div');
        $this->class[] = 'input-group';

        $this->children[] = $input;
    }

    public function prepend(Html $prependContent): Html
    {
        $prependWrapper = Html::el('div', [
            'class' => 'input-group-prepend',
        ]);
        $prependWrapper->children[] = $prependContent;

        $this->children[] = $prependWrapper;

        return $prependWrapper;
    }

    public function append(Html $appendContent): Html
    {
        $appendWrapper = Html::el('div', [
            'class' => 'input-group-append',
        ]);
        $appendWrapper->children[] = $appendContent;

        $this->children[] = $appendWrapper;

        return $appendContent;
    }

    /**
     * @param string|Html $content
     *
     * @return Html
     */
    public static function text($content): Html
    {
        $text = Html::el('div', ['class' => 'input-group-text']);
        $text->addHtml($content);

        return $text;
    }
}
