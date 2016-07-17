<?php

namespace App\Utils;

use App\Model\Services\Users;
use App\Model\Settings\AOption;
use App\Model\Settings\OptionBool;
use App\Model\Settings\OptionInt;
use App\Model\Settings\OptionString;
use App\Model\Settings\Settings;
use App\Utils\Migrations\Migrations;
use Nette\Utils\Strings;

final class EntityInitializer
{
    /** @var Settings */
    protected $settings;

    /** @var  Users */
    protected $users;

    public function __construct(Migrations $migrations)
    {
        $this->settings = $migrations->getService(Settings::class);
        $this->users = $migrations->getService(Users::class);
    }

    public function option($type, $title, $value, $handle = null)
    {
        if(!$handle){
            $handle = Strings::webalize($title);
        }
        $setting = $this->settings->findBy(['handle' => $handle]);
        if ($setting) {
            return 'Err- Option with handle' . $handle . ' already exists';
        }

        switch ($type){
            default:
                return "Err- Option type $type is not valid";
            case AOption::TYPE_STRING:
                $setting = new OptionString();
                break;
            case AOption::TYPE_BOOL:
                $setting = new OptionBool();
                break;
            case AOption::TYPE_INT:
                $setting = new OptionInt();
                break;
        }

        $setting->title = $title;
        $setting->handle = $handle;
        $setting->value = $value;

        $this->settings->save($setting);

        return "Ok - Option $handle added";
    }

    public function user($username, $password)
    {
        $result = $this->users->create($username, $password);
        if($result){
            return "OK - User $username was created succesfully.";
        } else {
            return "Err- User $username could not be added";
        }
    }
}
