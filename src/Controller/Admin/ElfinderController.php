<?php
namespace PlaygroundCore\Controller\Admin;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ElfinderController extends AbstractActionController
{

    protected $Config;

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
     * @return array|\Laminas\View\Model\ViewModel
     */
    public function indexAction()
    {
        $view = new ViewModel();
        $this->getConfig();
        $view->BasePath = $this->Config['BasePath'];
        $view->ConnectorPath = '/admin/elfinder/connector';

        return $view;
    }

    /**
     * @return \Laminas\View\Model\ViewModel
     */
    public function ckeditorAction()
    {
        $view = new ViewModel();
        $this->getConfig();
        $view->BasePath    = $this->Config['BasePath'];
        $view->ConnectorPath = '/admin/elfinder/connector';
        $view->setTerminal(true);

        return $view;
    }

    /**
     * @return \Laminas\View\Model\ViewModel
     */
    public function connectorAction()
    {
        $view = new ViewModel();
        $this->getConfig();

        $root = array();
        if (isset($this->Config['QuRoots'])) {
            $root = $this->Config['QuRoots'];
        }

        $opts = array(
            'debug' => false,
            'roots' => array($root)
        );

        $connector = new \elFinderConnector(new \elFinder($opts));
        $connector->run();

        $view->setTerminal(true);

        return $view;
    }

    /**
     * @param $attr
     * @param $path
     * @param $data
     * @param $volume
     *
     * @return bool|null
     */
    public function access($attr, $path, $data, $volume)
    {
        return strpos(basename($path), '.') === 0
            ? !($attr == 'read' || $attr == 'write')
            :  null;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        if (!$this->Config) {
            $config       = $this->getServiceLocator()->get('config');
            if (isset($config['playgroundcore']) && isset($config['playgroundcore']['QuConfig']) && isset($config['playgroundcore']['QuConfig']['QuElFinder'])) {
                $this->Config = $config['playgroundcore']['QuConfig']['QuElFinder'];
            } else {
                $this->Config = array();
            }
        }

        return $this->Config;
    }
}
