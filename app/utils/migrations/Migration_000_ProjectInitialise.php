<?php

namespace App\Utils\Migrations;


use App\Model\Settings\AOption;

class Migration_000_ProjectInitialise extends BaseMigration
{
    public function run()
    {
        $this->addOptions();
    }

    private function addOptions(){
        $this->add->option(AOption::TYPE_BOOL, 'Enable quotes', true);
    }
}
