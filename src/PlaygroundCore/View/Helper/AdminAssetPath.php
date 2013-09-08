<?php

namespace PlaygroundCore\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Exception;

/**
 * Helper for retrieving the Admin assets base path.
 */
class AdminAssetPath extends AbstractHelper
{
    /**
     * Base path
     *
     * @var string
     */
    protected $basePath;

    /**
     * Returns site's base path, or file with base path prepended.
     *
     * $file is appended to the base path for simplicity.
     *
     * @param  string|null $file
     * @throws Exception\RuntimeException
     * @return string
     */
    public function __invoke($file = null)
    {
        if (null === $this->basePath) {
            throw new Exception\RuntimeException('No base path provided');
        }

        if (null !== $file) {
            $file = '/' . ltrim($file, '/');
        }

        return $this->basePath . '/zfcadmin' .$file;
    }

    /**
     * Set the base path.
     *
     * @param  string $basePath
     * @return self
     */
    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '/');
        return $this;
    }
}
