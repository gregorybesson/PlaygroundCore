<?php
namespace PlaygroundCore\Service;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use ZfcBase\EventManager\EventProvider;

/**
 * main class
 */
class Ffmpeg extends EventProvider implements ServiceManagerAwareInterface
{

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * This method create a video from still images (jpg or png).
     * ffmpeg -framerate 1/5 -i etape%01d.jpg -c:v libx264 -vf "fps=25,format=yuv420p" out.mp4
     */
    public function createVideoFromImages($path = 'data/etape%01d.jpg', $target = false, $framerate = '1/5', $fps = 25)
    {
        // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
        $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
        $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
        ->addPreInputCommand('-framerate', $framerate)
        ->addCommand('-i', $path)
        ->addCommand('-c:v', 'libx264')
        ->addCommand('-vf', 'fps='. $fps .',format=yuv420p')
        ->setOutputPath($target)
        ->execute();
    }
    
    /**
     * This method will merge videos in .mpg format with exactly the same codec and codec parameters : http://trac.ffmpeg.org/wiki/Concatenate
     * @param  array $videos
     * @return string
     */
    public function mergeMpgVideos($videos = false, $target = false)
    {
        if(is_array($videos)){
            try
            {
                $concat = 'concat:';
                $i = 0;
                foreach($videos as $path){
                    if($i>0) $concat .= '|';
                    $concat .= $path;
                    $i++;
                }
                // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
                $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
                $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
                    ->addCommand('-i', $concat)
                    ->addCommand('-c', 'copy')
                    ->addCommand('-bsf:a', 'aac_adtstoasc')
                    ->setOutputPath($target)
                    ->execute();
            }
            catch(FfmpegProcessOutputException $e)
            {
                /*echo '<h1>Error</h1>';
                \PHPVideoToolkit\Trace::vars($e);
                $ffmpeg = $video->getProcess();
                if($ffmpeg->isCompleted())
                {
                    echo '<h1>Raw Executed Command</h1>';
                    \PHPVideoToolkit\Trace::vars($ffmpeg->getExecutedCommand(true));
                    echo '<hr /><h2>Executed Command</h2>';
                    \PHPVideoToolkit\Trace::vars($ffmpeg->getExecutedCommand());
                    echo '<hr /><h2>FFmpeg Process Messages</h2>';
                    \PHPVideoToolkit\Trace::vars($ffmpeg->getMessages());
                    echo '<hr /><h2>Buffer Output</h2>';
                    \PHPVideoToolkit\Trace::vars($ffmpeg->getBuffer(true));
                }*/
                throw new InvalidArgumentException('Error when merging videos');
            }
            catch(Exception $e)
            {
                /*echo '<h1>Error</h1>';
                \PHPVideoToolkit\Trace::vars($e->getMessage());
                echo '<h2>Exception</h2>';
                \PHPVideoToolkit\Trace::vars($e);
                */
                throw new InvalidArgumentException('Error when merging videos');
            }
        }

        return $target;
    }

    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions($this->getServiceManager()->get('playgroundcore_module_options'));
        }

        return $this->options;
    }

    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }
}
