<?php

namespace App\Utils;

use App\Services\Doctrine\Users;
use App\Utils\Migrations\Migrations;
use Nette\Utils\Strings;
use SeStep\Settings\Options;
use SeStep\Settings\Options\AOption;
use SeStep\Settings\Options\OptionBool;
use SeStep\Settings\Options\OptionInt;
use SeStep\Settings\Options\OptionString;
use SeStep\Settings\Settings;

final class EntityInitializer
{
    /** @var Options */
    protected $options;

    /** @var  Users */
    protected $users;

    public function __construct(Migrations $migrations)
    {
        $this->options = $migrations->getService(Settings::class);
        $this->users = $migrations->getService(Users::class);
    }

    public function option($type, $title, $value, $handle = null)
    {
        if (!$handle) {
            $handle = Strings::webalize($title);
        }
        $option = $this->options->findBy(['handle' => $handle]);
        if ($option) {
            return 'Err- Option with handle' . $handle . ' already exists';
        }

        switch ($type) {
            default:
                return "Err- Option type $type is not valid";
            case AOption::TYPE_STRING:
                $option = new OptionString();
                break;
            case AOption::TYPE_BOOL:
                $option = new OptionBool();
                break;
            case AOption::TYPE_INT:
                $option = new OptionInt();
                break;
        }

        $option->title = $title;
        $option->handle = $handle;
        $option->value = $value;

        $this->options->save($option);

        return "Ok - Option $handle added";
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
}
