<?php declare(strict_types=1);

namespace PAF\Common\Workflow;

use LeanMapper\Entity;
use LeanMapper\IMapper;
use PAF\Modules\AuditTrailModule\Facade\AuditTrailService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\WorkflowEvents;

class EntityWorkflowListener implements EventSubscriberInterface
{

    /** @var AuditTrailService */
    private $auditTrailService;
    /** @var IMapper */
    private $mapper;

    public function __construct(AuditTrailService $auditTrailService, IMapper $mapper, array $prefixByClass = [])
    {
        $this->auditTrailService = $auditTrailService;
        $this->mapper = $mapper;
    }

    public function transitionCompleted(CompletedEvent $event)
    {
        $subject = $event->getSubject();
        if (!$subject instanceof Entity) {
            return;
        }
        $primaryKey = $this->mapper->getPrimaryKey($this->mapper->getTable(get_class($subject)));

        $auditTrailEntryName = $this->getAuditTrailEntryName($event);
        $this->auditTrailService->addEvent($subject->$primaryKey, $auditTrailEntryName, [
            'from' => $event->getTransition()->getFroms(),
        ]);
        $this->auditTrailService->omitSubject($subject->$primaryKey, 1);
    }

    private function getAuditTrailEntryName(Event $event): string
    {
        $transitionName = $event->getTransition()->getName();
        $workflowName = $event->getWorkflowName();

        return "$workflowName.action.$transitionName";
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            WorkflowEvents::COMPLETED => 'transitionCompleted'
        ];
    }
}
