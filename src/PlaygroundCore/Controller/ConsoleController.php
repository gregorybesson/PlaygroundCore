<?php

namespace PlaygroundCore\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;
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

        $request = $this->getRequest();
        $response = $this->getResponse();

        /*if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('You can only use this action from a console!');
        }*/

        $cronjobs = $this->getCronService();

        $cronjobs->getCronjobs();

        if (count($cronjobs) > 0) {
            $cron  = $cronjobs->run();
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
