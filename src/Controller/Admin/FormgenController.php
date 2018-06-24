<?php
namespace PlaygroundCore\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorInterface;

class FormgenController extends AbstractActionController
{

    protected $formgenService;
    protected $websiteService;

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

    public function indexAction()
    {
        return array();
    }

    public function listAction()
    {
        $mapper = $this->getFormgenService()->getformgenMapper();
        $forms = $mapper->findAll();

        return new ViewModel(array(
            'forms' => $forms,
        ));
    }

    public function generateAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost()->toArray();
            $formGenService = $this->getFormgenService();
            $formGenService->insert($data);
            return $this->redirect()->toRoute('admin/formgen/list');
        }

        $websites = $this->getWebsiteService()->getWebsiteMapper()->findAll();
        return new ViewModel(array(
            'websites' => $websites,
        ));
    }
    public function editAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost()->toArray();
            $formGenService = $this->getFormgenService();
            $formgen = $this->getFormgenService()->getFormgenMapper()->findById($data['formId']);
            $formGenService->update($formgen, $data);

            return $this->redirect()->toRoute('admin/formgen/list');
        }

        $formId = $this->getEvent()->getRouteMatch()->getParam('formId');
        $formgen = $formGenService = $this->getFormgenService()->getFormgenMapper()->findById($formId);


        $websites = $this->getWebsiteService()->getWebsiteMapper()->findAll();
        return new ViewModel(array(
           'websites' => $websites,
           'form' => $formgen,
        ));
    }

    public function activateAction()
    {
        $formId = $this->getEvent()->getRouteMatch()->getParam('formId');
        $formgen = $this->getFormgenService()->getFormgenMapper()->findById($formId);
        $formgen->setActive(!$formgen->getActive());
        $this->getFormgenService()->getFormgenMapper()->update($formgen);

        return $this->redirect()->toRoute('admin/formgen/list');
    }

    public function viewAction()
    {
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $headScript = $this->getServiceLocator()->get('ViewHelperManager')->get('HeadScript');
        $headScript->appendFile($renderer->adminAssetPath() . '/js/form/parse.form.js');

        $formId = $this->params('form');

        return array('form_id' => $formId);
    }

    public function createAction()
    {
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $headScript = $this->getServiceLocator()->get('ViewHelperManager')->get('HeadScript');
        $headScript->appendFile($renderer->adminAssetPath() . '/js/form/create.form.js');
        $headScript->appendFile($renderer->adminAssetPath() . '/js/form/line.text.js');
        $headScript->appendFile($renderer->adminAssetPath() . '/js/form/add.form.js');
        $headScript->appendFile($renderer->adminAssetPath() . '/js/form/json.form.js');
        $headScript->appendFile($renderer->adminAssetPath() . '/js/form/edit.form.js');

        return array();
    }

    public function inputAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function passwordAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function passwordverifyAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function numberAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function phoneAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function paragraphAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function checkboxAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function radioAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function dropdownAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function emailAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function dateAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function uploadAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function creditcardAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function urlAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function hiddenAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function getAjax()
    {
        $request = $this->getRequest();
        $results = $request->getQuery();

        $result = new ViewModel(array(
                'result' => $results,
        ));

        $result->setTerminal(true);

        return $result;
    }

    public function getFormgenService()
    {
        if (!$this->formgenService) {
            $this->formgenService = $this->getServiceLocator()->get('playgroundcore_formgen_service');
        }

        return $this->formgenService;
    }

    public function getWebsiteService()
    {
        if (!$this->websiteService) {
            $this->websiteService = $this->getServiceLocator()->get('playgroundcore_website_service');
        }

        return $this->websiteService;
    }

    public function setFormgenService($formgenService)
    {
        $this->formgenService = $formgenService;

        return $this;
    }
}
