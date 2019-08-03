<?php declare(strict_types=1);

namespace PAF\Common\Forms;

use Nette\Forms\Controls;
use Nette\Forms\Validator;
use Nette\Localization\ITranslator;
use Nette\SmartObject;

class FormFactory
{
    use SmartObject;

    /** @var ITranslator */
    private $translator;

    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return Form
     */
    public function create()
    {
        $form = new Form();
        $form->setTranslator($this->translator);

        $form->setRenderer(new BootstrapFormRenderer());

        return $form;
    }

    public static function adjustValidatorMessages()
    {
        Validator::$messages = [
            Form::EQUAL                   => 'generic.form-validator.equal',
            Form::NOT_EQUAL               => 'generic.form-validator.not-equal',
            Form::FILLED                  => 'generic.form-validator.filled',
            Form::BLANK                   => 'generic.form-validator.blank',
            Form::MIN_LENGTH              => 'generic.form-validator.min-length',
            Form::MAX_LENGTH              => 'generic.form-validator.max-length',
            Form::LENGTH                  => 'generic.form-validator.length',
            Form::EMAIL                   => 'generic.form-validator.email',
            Form::URL                     => 'generic.form-validator.url',
            Form::INTEGER                 => 'generic.form-validator.integer',
            Form::FLOAT                   => 'generic.form-validator.float',
            Form::MIN                     => 'generic.form-validator.min',
            Form::MAX                     => 'generic.form-validator.max',
            Form::RANGE                   => 'generic.form-validator.range',
            Form::MAX_FILE_SIZE           => 'generic.form-validator.max-file-size',
            Form::MAX_POST_SIZE           => 'generic.form-validator.max-post-size',
            Form::MIME_TYPE               => 'generic.form-validator.mime-type',
            Form::IMAGE                   => 'generic.form-validator.image',
            Controls\SelectBox::VALID     => 'generic.form-validator.select-valid',
            Controls\UploadControl::VALID => 'generic.form-validator.upload-valid',
        ];
    }
}
