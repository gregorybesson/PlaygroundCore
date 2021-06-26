<?php
namespace PlaygroundCore\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Helper\HeadMeta;
use PlaygroundCore\Opengraph\Tracker;
use Laminas\Stdlib\RequestInterface;
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

    public function __invoke()
    {
        if ($this->rendered) {
            return;
        }

        $tracker = $this->tracker;
        if (!$tracker->enabled()) {
            return;
        }

        if ((get_class($this->request) == 'Laminas\Console\Request')) {
            return;
        }

        $container = $this->view->plugin($this->getContainer());
        if (!$container instanceof HeadMeta) {
            throw new RuntimeException(sprintf(
                'Container %s does not extend HeadMeta view helper',
                $this->getContainer()
            ));
        }

        $container->appendName('fb:app', $tracker->getId());

        if (null !== ($tags = $tracker->tags())) {
            foreach ($tags as $tag) {
                $container->appendProperty($tag->getProperty(), $tag->getValue());
            }
        }

        $this->rendered = true;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function setContainer($container)
    {
        $this->container = $container;
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
