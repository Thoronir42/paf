<?php declare(strict_types=1);

namespace PAF\Modules\Front\Presenters;

use Nette;
use Tracy\ILogger;

class ErrorPresenter implements Nette\Application\IPresenter
{
    use Nette\SmartObject;

    /** @var ILogger */
    private $logger;


    public function __construct(ILogger $logger)
    {
        $this->logger = $logger;
    }


    /**
     * @param Nette\Application\Request $request
     * @return Nette\Application\IResponse
     */
    public function run(Nette\Application\Request $request): Nette\Application\IResponse
    {
        $e = $request->getParameter('exception');

        if ($e instanceof Nette\Application\BadRequestException) {
            return new Nette\Application\Responses\ForwardResponse($request->setPresenterName('Front:Error4xx'));
        }

        $this->logger->log($e, ILogger::EXCEPTION);

        return new Nette\Application\Responses\CallbackResponse(function () {
            require __DIR__ . '/../templates/Error/500.phtml';
        });
    }
}
