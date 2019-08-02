<?php declare(strict_types=1);

namespace PAFData\Fixtures\Modules\CommonModule;

use PAFData\Fixtures\InitializerModule;
use Symfony\Component\Console\Output\OutputInterface;

class CommonInitializer extends InitializerModule
{
    /** @var CommonEntityCreator */
    private $add;

    /**
     * CommonInitializer constructor.
     * @param CommonEntityCreator $creator
     */
    public function __construct(CommonEntityCreator $creator)
    {
        $this->add = $creator;
    }

    public function setOutput(OutputInterface $output = null): void
    {
        parent::setOutput($output);
        $this->add->setOutput($output);
    }


    public function run(): int
    {
        $this->addUsers();

        return 0;
    }

    private function addUsers()
    {
        $this->add->user('Toanir', 'test');
        $this->add->user('Toust', 'test');
    }
}
