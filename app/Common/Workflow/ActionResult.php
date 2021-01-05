<?php declare(strict_types=1);

namespace PAF\Common\Workflow;

class ActionResult
{
    private bool $success;
    private ?string $message;
    private array $params;

    public function __construct(bool $success, string $message = null, array $params = [])
    {
        $this->success = $success;
        $this->message = $message;
        $this->params = $params;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public static function illegalAction(
        string $state,
        string $attemptedAction,
        array $availableActions = null
    ): ActionResult {
        $params = [
            'state' => $state,
            'attemptedAction' => $attemptedAction,
        ];
        if ($availableActions) {
            $params['availableActions'] = $availableActions;
        }

        return new ActionResult(false, 'paf.workflow.illegalAction', $params);
    }
}
