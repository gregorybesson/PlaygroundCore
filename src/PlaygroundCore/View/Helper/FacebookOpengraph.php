<?php
namespace PlaygroundCore\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\HeadMeta;
use PlaygroundCore\Opengraph\Tracker;
use Zend\Stdlib\RequestInterface;

use PlaygroundCore\Exception\RuntimeException;

class FacebookOpengraph extends AbstractHelper
{
    /**
     * @var Tracker
     */
    protected $tracker;

    /**
     * @var string
     */
    protected $container = 'HeadMeta';

    /**
     * @var bool
     */
    protected $rendered = false;
    protected $request;

    public function __construct(Tracker $tracker, RequestInterface $request)
    {
        $this->tracker = $tracker;
        $this->request = $request;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function __invoke()
    {
        // Do not render the GA twice
        if ($this->rendered) {
            return;
        }

        // Do not render when tracker is disabled
        $tracker = $this->tracker;
        if (!$tracker->enabled()) {
            return;
        }

        // We return if we are in a console request
        if ((get_class($this->request) == 'Zend\Console\Request')) {
            return;
        }

        // We need to be sure $container->appendProperty() can be called
        $container = $this->view->plugin($this->getContainer());
        if (!$container instanceof HeadMeta) {
            throw new RuntimeException(sprintf(
                'Container %s does not extend HeadMeta view helper',
                 $this->getContainer()
            ));
        }

        $container->appendProperty('fb:app', $tracker->getId());

        if (null !== ($tags = $tracker->tags())) {
            foreach ($tags as $tag) {
                $container->appendProperty($tag->getProperty(), $tag->getValue());
            }
        }

        // Mark this OG as rendered
        $this->rendered = true;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }
}
