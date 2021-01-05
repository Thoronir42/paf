<?php declare(strict_types=1);

namespace PAF\Modules\Settings\Presenters;

use Nette\Application\BadRequestException;
use Nette\Application\UI\Presenter;
use Nette\Http\IResponse;
use Nette\Utils\Json;
use SeStep\GeneralSettings\Settings;

class SettingsApiPresenter extends Presenter
{
    /** @inject */
    public Settings $settings;

    public function actionOptionEndpoint(string $fqn)
    {
        switch ($this->request->method) {
            case 'GET':
                $this->sendJson(['value' => $this->settings->getValue($fqn)]);
                break;

            case 'PUT':
                $body = Json::decode($this->getHttpRequest()->getRawBody());
                if (!$body || !property_exists($body, 'value')) {
                    throw new BadRequestException("Malformed body, property 'value not present'");
                }

                $previousValue = $this->settings->getValue($fqn);
                $this->settings->setValue($fqn, $body->value);
                $this->sendJson(['value' => $this->settings->getValue($fqn), 'previousValue' => $previousValue]);
                break;

            default:
                throw new BadRequestException("Unsupported", IResponse::S405_METHOD_NOT_ALLOWED);
        }
    }
}
