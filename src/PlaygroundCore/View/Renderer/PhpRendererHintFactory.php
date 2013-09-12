<?php

namespace PlaygroundCore\View\Renderer;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use PlaygroundCore\View\Renderer\PhpRendererHint;

class PhpRendererHintFactory implements FactoryInterface
{
	protected $helperManager;
	protected $resolver;
	
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		
		$renderer = new PhpRendererHint();
		$renderer->setHelperPluginManager($serviceLocator->get('ViewHelperManager'));
		$renderer->setResolver($serviceLocator->get('ViewResolver'));
		
		/*$model       = $this->getViewModel();
		$modelHelper = $this->renderer->plugin('view_model');
		$modelHelper->setRoot($model);*/
		
		return $renderer;
	}
}