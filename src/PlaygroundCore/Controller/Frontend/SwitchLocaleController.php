<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace PlaygroundCore\Controller\Frontend;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Controller\AbstractActionController;

class SwitchLocaleController extends AbstractActionController implements ServiceLocatorAwareInterface
{
    /**
    * @var $localeService : Service des locales
    */
    protected $localeService;

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
        $locale = $this->getEvent()->getRouteMatch()->getParam('locale');
        $context = $this->getEvent()->getRouteMatch()->getParam('area');
        $redirect = (!empty($this->getEvent()->getRouteMatch()->getParam('redirect')))? urldecode($this->getEvent()->getRouteMatch()->getParam('redirect')) : '/';

        $cookie = new \Zend\Http\Header\SetCookie('pg_locale_'.$context, $locale, time() + 60*60*24*365, '/');
        $this->getResponse()->getHeaders()->addHeader($cookie);

        return $this->redirect()->toUrl($redirect);
    }


    /**
    * getServiceLocator : Recuperer le service locator
    * @return ServiceLocator $serviceLocator
    */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
    * setServiceLocator : set le service locator
    */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

      /**
    * getLocaleService : Recuperer le service des locales
    *
    * @return Service/Locale $localeService
    */
    public function getLocaleService()
    {
        if ($this->localeService === null) {
            $this->localeService = $this->getServiceLocator()->get('playgroundcore_locale_service');
        }
        return $this->localeService;
    }
}
