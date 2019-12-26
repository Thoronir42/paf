<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Model;

use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CommissionWorkflow extends Workflow
{
    const STATUS_ACCEPTED = "accepted";
    const STATUS_WIP = "wip";
    const STATUS_FINISHED = "finished";
    const STATUS_SHIPPED = "shipped";
    const STATUS_CANCELLED = "cancelled";

    const ACTION_COMMENCE = 'commence';
    const ACTION_FINISH = 'finish';
    const ACTION_SHIP = 'ship';
    const ACTION_CANCEL = 'cancel';

    public function __construct(EventDispatcherInterface $eventDispatcher = null)
    {
        parent::__construct(
            self::createDefinition(),
            new MethodMarkingStore(true, 'state'),
            $eventDispatcher,
            'commission.commissionWorkflow'
        );
    }


    /**
     * @return string[]
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACCEPTED,
            self::STATUS_WIP,
            self::STATUS_FINISHED,
            self::STATUS_SHIPPED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * @return Transition[]
     */
    public static function getTransitions(): array
    {
        return [
            new Transition(self::ACTION_COMMENCE, self::STATUS_ACCEPTED, self::STATUS_WIP),
            new Transition(self::ACTION_FINISH, self::STATUS_WIP, self::STATUS_FINISHED),
            new Transition(self::ACTION_SHIP, self::STATUS_FINISHED, self::STATUS_SHIPPED),
            new Transition(self::ACTION_CANCEL, self::STATUS_ACCEPTED, self::STATUS_CANCELLED),
            new Transition(self::ACTION_CANCEL, self::STATUS_WIP, self::STATUS_CANCELLED),
        ];
    }

    private static function createDefinition(): Definition
    {
        return new Definition(self::getStatuses(), self::getTransitions(), [self::STATUS_ACCEPTED]);
    }

    /**
     * @return string[]
     */
    public static function getStatusesLocalized()
    {
        $states = [];
        foreach (CommissionWorkflow::getStatuses() as $status) {
            $states[$status] = "commission.commission.status.$status";
        }

        return $states;
    }

    /**
     * @param Commission $subject
     * @return string[]
     */
    public function getActionsLocalized(Commission $subject): array
    {
        $actions = [];
        foreach ($this->getEnabledTransitions($subject) as $transition) {
            $actions[$transition->getName()] = 'commission.commission.action.' . $transition->getName();
        }

        return $actions;
    }
}
