<?php declare(strict_types=1);

namespace PAF\Common\Workflow;

use LeanMapper\Entity;
use LeanMapper\IMapper;
use SeStep\NetteAuditTrail\Facade\AuditTrailService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\WorkflowEvents;

class EntityWorkflowListener implements EventSubscriberInterface
{
    private AuditTrailService $auditTrailService;
    private IMapper$mapper;

    public function __construct(AuditTrailService $auditTrailService, IMapper $mapper)
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

    public static function getSubscribedEvents(): array
    {
        return [
            WorkflowEvents::COMPLETED => 'transitionCompleted'
        ];
    }
}
