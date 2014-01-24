<?php
namespace PlaygroundCore\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;

class FormgenController extends AbstractActionController
{

    protected $formgenService;
    protected $websiteService;


    public function indexAction()
    {
        return array ();
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
                $formGenService->update($data);
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
        $formgen = $formGenService = $this->getFormgenService()->getFormgenMapper()->findById($formId);
        $formgen->setActive(!$formgen->getActive());
        $formgen = $formGenService = $this->getFormgenService()->getFormgenMapper()->update($formgen);
        return $this->redirect()->toRoute('admin/formgen/list');
    }

    public function viewAction()
    {
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $headScript = $this->getServiceLocator()->get('viewhelpermanager')->get('HeadScript');
        $headScript->appendFile ( $renderer->adminAssetPath() . '/js/form/parse.form.js' );

        $formId = $this->params('form');

        return array('form_id' => $formId);
    }

    public function testAction()
    {
        $form = new AddUser();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $user = new User();
            $formValidator = new AddUserValidator();

            $form->setInputFilter($formValidator->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $user->exchangeArray($form->getData());
            }
        }

        return array('form' => $form);
    }

    public function createAction()
    {
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $headScript = $this->getServiceLocator()->get('viewhelpermanager')->get('HeadScript');
        $headScript->appendFile ( $renderer->adminAssetPath() . '/js/form/create.form.js' );
        $headScript->appendFile ( $renderer->adminAssetPath() . '/js/form/line.text.js' );
        $headScript->appendFile ( $renderer->adminAssetPath() . '/js/form/add.form.js' );
        $headScript->appendFile ( $renderer->adminAssetPath() . '/js/form/json.form.js' );
        $headScript->appendFile ( $renderer->adminAssetPath() . '/js/form/edit.form.js' );

        //$form = '';
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
        $request = $this->getRequest ();
        $results = $request->getQuery ();

        $result = new ViewModel (array(
                'result' => $results,
        ));

        $result->setTerminal ( true );

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
