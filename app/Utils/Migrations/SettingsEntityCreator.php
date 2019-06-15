<?php declare(strict_types=1);

namespace PAF\Utils\Migrations;


use Nette\Utils\Strings;
use SeStep\Migrations\IServiceProvider;
use SeStep\SettingsDoctrine\DoctrineOptions;
use SeStep\SettingsDoctrine\Options\OptionsSection;
use SeStep\SettingsInterface\Exceptions\NotFoundException;
use Symfony\Component\Console\Output\OutputInterface;

class SettingsEntityCreator
{
    /** @var DoctrineOptions */
    protected $options;

    /** @var OutputInterface */
    protected $output;

    public function __construct(IServiceProvider $provider, OutputInterface $output)
    {
        $this->options = $provider->getService(DoctrineOptions::class);

        $this->output = $output;
    }

    public function section($name, $caption = '', OptionsSection $parent = null)
    {
        $section = $this->options->createSection($name, $caption, $parent);

        $this->options->save($section);

        return $section;
    }

    public function option($type, $caption, $value, $name = null, OptionsSection $section = null)
    {
        if (!$name) {
            $name = Strings::webalize($caption);
        }

        try {
            $option = $this->options->getOption($name, $section);

            $this->output->writeln('Err- Option ' . $option->getFQN() . ' already exists');

            return $option;
        } catch (NotFoundException $exception) {
            $option = $this->options->createOption($type, $name, $value, $caption, $section);
            $this->options->save($option);

            $this->output->writeln('Ok - Option ' . $option->getFQN() . ' added');

            return $option;
        }
    }
}
