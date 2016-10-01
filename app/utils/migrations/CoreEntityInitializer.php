<?php

namespace App\Utils\Migrations;

use App\Services\Doctrine\Users;
use App\Utils\Migrations\Migrations;
use Kdyby\Doctrine\InvalidStateException;
use Nette\Utils\Strings;
use SeStep\Migrations\IServiceProvider;
use SeStep\SettingsDoctrine\DoctrineOptions;
use SeStep\SettingsDoctrine\Options\OptionsSection;
use SeStep\SettingsInterface\Exceptions\OptionNotFoundException;
use SeStep\SettingsInterface\Exceptions\OptionsSectionNotFoundException;
use Symfony\Component\Console\Output\OutputInterface;

class CoreEntityInitializer
{
    /** @var DoctrineOptions */
    protected $options;
    /** @var Users */
    protected $users;

    /** @var OutputInterface */
    protected $output;

    public function __construct(IServiceProvider $provider, OutputInterface $output)
    {
        $this->options = $provider->getService(DoctrineOptions::class);
        $this->users = $provider->getService(Users::class);

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
        } catch (OptionNotFoundException $exception) {
            $option = $this->options->createOption($type, $name, $value, $caption, $section);
            $this->options->save($option);

            $this->output->writeln('Ok - Option ' . $option->getFQN() . ' added');

            return $option;
        }
    }

    public function user($username, $password)
    {
        $result = $this->users->findOneByUsername($username);
        if ($result) {
            $this->output->writeln("OK - User $username was created succesfully.");

            return $result;
        } else {
            $user = $this->users->create($username, $password);
            $this->output->writeln("Err- User $username could not be added");

            return $user;
        }
    }
}
