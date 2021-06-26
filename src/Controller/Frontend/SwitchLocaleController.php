<?php
namespace PlaygroundCore\Controller\Frontend;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\ServiceManager\ServiceLocatorInterface;

class SwitchLocaleController extends AbstractActionController
{
    /**
     *
     * @var ServiceManager
     */
    protected $serviceLocator;

    public function __construct(ServiceLocatorInterface $locator)
    {
        $this->serviceLocator = $locator;
    }

    public function getServiceLocator()
    {
        
        return $this->serviceLocator;
    }

    /**
    * switchAction : permet de switcher de langue en fonction d'un context (back/front)
    * locale : locale pour switch
    * context : (back/front)
    * referer : retour à la page
    *
    * @return Redirect $redirect redirect to referer
    */
    public function switchAction()
    {
        $lang = $this->getEvent()->getRouteMatch()->getParam('lang');
        $context = $this->getEvent()->getRouteMatch()->getParam('area');
        $redirect = (!empty($this->getEvent()->getRouteMatch()->getParam('redirect')))? urldecode($this->getEvent()->getRouteMatch()->getParam('redirect')) : '/'.$lang;

        $cookie = new \Laminas\Http\Header\SetCookie('pg_locale_'.$context, $lang, time() + 60*60*24*365, '/');
        $this->getResponse()->getHeaders()->addHeader($cookie);

        return $this->redirect()->toUrl($redirect);
    }
}
