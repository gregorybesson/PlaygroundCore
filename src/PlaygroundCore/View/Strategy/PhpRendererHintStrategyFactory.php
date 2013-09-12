<?php
namespace PlaygroundCore\View\Strategy;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PhpRendererHintStrategyFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$viewRenderer = $serviceLocator->get('PhpRendererHint');
		return new PhpRendererHintStrategy($viewRenderer);
	}
}