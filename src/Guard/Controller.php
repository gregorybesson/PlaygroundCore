<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace PlaygroundCore\Guard;

use BjyAuthorize\Exception\UnAuthorizedException;
use Laminas\Console\Request as ConsoleRequest;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Http\Request as HttpRequest;
use Laminas\Mvc\MvcEvent;

/**
 * Controller Guard listener, allows checking of permissions
 * during {@see \Laminas\Mvc\MvcEvent::EVENT_DISPATCH}
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class Controller extends \BjyAuthorize\Guard\Controller
{
    /**
     * Event callback to be triggered on dispatch, causes application error triggering
     * in case of failed authorization check
     * *** GRG: Addition of try catch when no role has been found in the database ***
     *
     * @param MvcEvent $event
     *
     * @return mixed
     */
    public function onDispatch(MvcEvent $event)
    {
        /* @var $service \BjyAuthorize\Service\Authorize */
        $service = $this->container->get('BjyAuthorize\Service\Authorize');
        $match = $event->getRouteMatch();
        $controller = $match->getParam('controller');
        $action = $match->getParam('action');
        $request = $event->getRequest();
        $method = $request instanceof HttpRequest ? strtolower($request->getMethod()) : null;

        try {
        $authorized = (class_exists(ConsoleRequest::class) && $event->getRequest() instanceof ConsoleRequest)
            || $service->isAllowed($this->getResourceName($controller))
            || $service->isAllowed($this->getResourceName($controller, $action))
            || ($method && $service->isAllowed($this->getResourceName($controller, $method)));
        } catch (\Laminas\Permissions\Acl\Exception\InvalidArgumentException $e) {
            $authorized = false;
            $errorMessage = $e->getMessage();
            $event->setParam('exception', new \Exception($errorMessage));
            $event->setError(\Laminas\Mvc\Application::ERROR_EXCEPTION);
        }
        if ($authorized) {
            return;
        }

        $event->setParam('identity', $service->getIdentity());
        $event->setParam('controller', $controller);
        $event->setParam('action', $action);

        if (empty($errorMessage)) {
            $event->setError(static::ERROR);
            $errorMessage = sprintf("You are not authorized to access %s:%s", $controller, $action);
            $event->setParam('exception', new UnAuthorizedException($errorMessage));
        }

        /* @var $app \Laminas\Mvc\ApplicationInterface */
        $app = $event->getTarget();
        $eventManager = $app->getEventManager();
        $eventManager->setEventPrototype($event);

        $results = $eventManager->trigger(
            MvcEvent::EVENT_DISPATCH_ERROR,
            null,
            $event->getParams()
        );
        $return  = $results->last();
        if (! $return) {
            return $event->getResult();
        }

        return $return;
    }
}
