<?php

namespace PlaygroundCore\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Response as ConsoleResponse;
use PlaygroundCore\Service\Cron;

class ConsoleController extends AbstractActionController
{

    /**
     * @var cronService
     */
    protected $cronService;

    public function cronAction()
    {
        $response = $this->getResponse();

        $cronjobs = $this->getCronService();

        $cronjobs->getCronjobs();

        if (count($cronjobs) > 0) {
            $cronjobs->run();
        }

        if (!$response instanceof ConsoleResponse) {
            $response->setStatusCode(200);
            $response->setContent('ok');

            return $response;
        } else {
            return 'ok';
        }
    }

    public function getCronService()
    {
        if (!$this->cronService) {
            $this->cronService = $this->getServiceLocator()->get('playgroundcore_cron_service');
        }

        return $this->cronService;
    }

    public function setCronService(Cron $cronService)
    {
        $this->cronService = $cronService;

        return $this;
    }
}
