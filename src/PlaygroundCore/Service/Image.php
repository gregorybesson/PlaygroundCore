<?php
namespace PlaygroundCore\Service;

use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * require php_exif for some methods
 */
class Image extends EventProvider implements ServiceManagerAwareInterface
{
    
    protected $file;
    
    /**
     * @var resource
     */
    protected $image;
    
    /**
     * @param string $file
     * @throws \Exception if the file does not exists
     * @return \PlaygroundCore\Service\Image
     */
    public function setImage($file)
    {
        if (!file_exists($file)) {
            throw new \Exception('Not a file: "' . $file . '"', null, null);
        }
        $this->file = $file;
        $this->image = imagecreatefromstring(
            file_get_contents($file)
        );
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function canCorrectOrientation()
    {
        return function_exists('exif_read_data')
                && (substr($this->file, -strlen('.jpg')) === '.jpg'
                    || substr($this->file, -strlen('.jpeg')) === '.jpeg');
    }
    
    /**
     * Correct image orientation (if present in the medadata)
     * use php_exif
     * @return \PlaygroundCore\Service\Image
     */
    public function correctOrientation()
    {
        try {
            $exif = exif_read_data($this->file);
        } catch (\Exception $e) {
            return $this;
        }
        if (!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 8:
                    $this->image = imagerotate($this->image, 90, 0);
                    break;
                case 3:
                    $this->image = imagerotate($this->image, 180, 0);
                    break;
                case 6:
                    $this->image = imagerotate($this->image, -90, 0);
                    break;
            }
        }
        return $this;
    }
    
    /**
     * save as jpeg
     * @param string $path
     * @return \PlaygroundCore\Service\Image
     */
    public function save($path = null)
    {
        if (is_null($path)) {
            $path = $this->file;
        }
        imagejpeg($this->image, $path);
        return $this;
    }

    /**
     * @return \Zend\ServiceManager\ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }
    
    /**
     * output as jpeg
     */
    public function __toString()
    {
        echo imagejpeg($this->image);
    }

    /**
     * @param ServiceManager $serviceManager
     * @return \PlaygroundCore\Service\Image
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
}
