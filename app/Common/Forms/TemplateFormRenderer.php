<?php declare(strict_types=1);

namespace PAF\Common\Forms;


use Nette\Application\UI\ITemplateFactory;
use Nette\FileNotFoundException;
use Nette\Forms\Form;
use Nette\Forms\IFormRenderer;

class TemplateFormRenderer implements IFormRenderer
{
    /** @var ITemplateFactory */
    private $templateFactory;
    /** @var string */
    private $templateFile;

    public function __construct(ITemplateFactory $templateFactory, string $templateFile)
    {
        if(!file_exists($templateFile)) {
            throw new FileNotFoundException("File '$templateFile' does not exists");
        }

        $this->templateFactory = $templateFactory;
        $this->templateFile = $templateFile;
    }
    /**
     * @inheritDoc
     */
    function render(Form $form): string
    {
        $template = $this->templateFactory->createTemplate();
        $template->form = $form;
        $template->setFile($this->templateFile);

        return (string)$template;
    }
}
