<?php
namespace PlaygroundCore\Controller\Frontend;

use Zend\Mvc\Controller\AbstractActionController;

class SwitchLocaleController extends AbstractActionController
{

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

        $cookie = new \Zend\Http\Header\SetCookie('pg_locale_'.$context, $lang, time() + 60*60*24*365, '/');
        $this->getResponse()->getHeaders()->addHeader($cookie);

        return $this->redirect()->toUrl($redirect);
    }
}
