<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace PlaygroundCore\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class WebsiteAdminController extends AbstractActionController
{

	protected $websiteService;

	protected $localeService;

	public function indexAction()
	{
		return new ViewModel();
	}

	public function listAction()
	{
        $locales = $this->getLocaleService()->getLocaleMapper()->findBy(array('active_front' => 1));
        $user = $this->zfcUserAuthentication()->getIdentity();

        $websites = $this->getWebsiteService()->getWebsiteMapper()->findAll();

        return new ViewModel(compact("websites", "locales", "user"));
	}

	public function editActiveAction()
	{
		$websiteId = $this->getEvent()->getRouteMatch()->getParam('websiteId');
		$website = $this->getWebsiteService()->getWebsiteMapper()->findBy(array('id' => $websiteId));
		$website = $website[0];

        if($website->getDefault()) {

            return $this->redirect()->toRoute('admin');
        }
		$website->setActive(!$website->getActive());
		$this->getWebsiteService()->getWebsiteMapper()->update($website);

        return $this->redirect()->toRoute('admin');
	}

	public function getWebsiteService()
    {
        if (null === $this->websiteService) {
            $this->websiteService = $this->getServiceLocator()->get('playgroundcore_website_service');
        }

        return $this->websiteService;
    }

    public function setWebsiteService($websiteService)
    {
        $this->websiteService = $websiteService;

        return $this;
    }

    public function getLocaleService()
    {
        if (null === $this->localeService) {
            $this->localeService = $this->getServiceLocator()->get('playgroundcore_locale_service');
        }

        return $this->localeService;
    }

    public function setLocaleService($localeService)
    {
        $this->localeService = $localeService;

        return $this;
    }
}
