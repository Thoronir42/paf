<?php

namespace App\Utils;

use App\Services\Doctrine\Users;
use App\Utils\Migrations\Migrations;
use Kdyby\Doctrine\InvalidStateException;
use Nette\Utils\Strings;
use SeStep\SettingsDoctrine\DoctrineOptions;
use SeStep\SettingsDoctrine\Options\OptionsSection;
use SeStep\SettingsInterface\Exceptions\OptionNotFoundException;
use SeStep\SettingsInterface\Exceptions\OptionsSectionNotFoundException;

final class EntityInitializer
{
    /** @var DoctrineOptions */
    protected $options;

    /** @var  Users */
    protected $users;

    public function __construct(Migrations $migrations)
    {
        $this->options = $migrations->getService(DoctrineOptions::class);
        $this->users = $migrations->getService(Users::class);
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

            return 'Err- Option ' . $option->getFQN() . ' already exists';
        } catch (OptionNotFoundException $exception) {
            $option = $this->options->createOption($type, $name, $value, $caption, $section);
            $this->options->save($option);

            return 'Ok - Option ' . $option->getFQN() . ' added';
        }
    }

    public function user($username, $password)
    {
        $result = $this->users->create($username, $password);
        if ($result) {
            return "OK - User $username was created succesfully.";
        } else {
            return "Err- User $username could not be added";
        }
    }

    public function flushAll()
    {
        $this->options->flushAll();
    }
}
