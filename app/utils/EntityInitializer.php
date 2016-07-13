<?php

namespace App\Utils;

use App\Model\Settings\AOption;
use App\Model\Settings\OptionBool;
use App\Model\Settings\OptionInt;
use App\Model\Settings\OptionString;
use App\Model\Settings\Settings;
use App\Utils\Migrations\Migrations;
use Nette\InvalidArgumentException;
use Nette\Utils\Strings;

final class EntityInitializer
{
    /** @var Settings */
    protected $settings;

    public function __construct(Migrations $migrations)
    {
        $this->settings = $migrations->getService(Settings::class);
    }

    public function option($type, $title, $value, $handle = null)
    {
        if(!$handle){
            $handle = Strings::webalize($title);
        }
        $setting = $this->settings->findBy(['handle' => $handle]);
        if ($setting) {
            return 'duplication of HANDLE ' . $handle . ', pick another';
        }

        switch ($type){
            default:
                throw new InvalidArgumentException('Option type ' . $type . ' is not a valid type.');
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

        return 'SETTING ' . $handle . ' added';
    }
}
