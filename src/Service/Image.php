<?php
namespace PlaygroundCore\Service;

use Laminas\EventManager\EventManagerAwareTrait;
use Laminas\ServiceManager\ServiceManager;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * require php_exif for some methods
 */
class Image
{
    use EventManagerAwareTrait;

    protected $file;
    
    /**
     * @var resource
     */
    protected $image;
    
    /**
     *
     * @var ServiceManager
     */
    protected $serviceLocator;

    public function __construct(ServiceLocatorInterface $locator)
    {
        $this->serviceLocator = $locator;
    }

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
     * output as jpeg
     */
    public function __toString()
    {
        echo imagejpeg($this->image);
    }
}
