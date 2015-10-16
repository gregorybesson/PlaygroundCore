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
    public function createVideoFromImages($path = 'data/etape*.jpg', $target = false, $framerate = '25', $fps = 25)
    {
        try {
            // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
            $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
            /*
                ->addPreInputCommand('-y') : overwrite existing file
            */
            $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addPreInputCommand('-framerate', $framerate)
            //->addPreInputCommand('-start_number', '00154')
            ->addPreInputCommand('-pattern_type', 'glob') 
            ->addCommand('-i', $path)
            ->addCommand('-c:v', 'libx264')
            ->addCommand('-vf', 'fps='. $fps)
            ->addCommand('-pix_fmt', 'yuv420p')
            ->setOutputPath($target)
            ->execute();

            //\PHPVideoToolkit\Trace::vars($ffmpeg->getExecutedCommand(true));
        } catch(FfmpegProcessOutputException $e) {
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
        } catch(Exception $e) {
            /*echo '<h1>Error</h1>';
            \PHPVideoToolkit\Trace::vars($e->getMessage());
            echo '<h2>Exception</h2>';
            \PHPVideoToolkit\Trace::vars($e);
            */
            throw new InvalidArgumentException('Error when merging videos');
        }

        return $target;
    }

    /**
     * This method create a video from still images (jpg or png).
     * ffmpeg -framerate 1/5 -i etape%01d.jpg -c:v libx264 -vf "fps=25,format=yuv420p" out.mp4
     */
    public function createMovFromAlphaImages($path = 'data/etape*.jpg', $target = false, $framerate = '25', $fps = 25)
    {
        // '-vcodec' 'qtrle' '/Users/grg/programmation/git/quickburger/public/frontend/assets/video/game/grg/intro-scene.mov'
        try {
            // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
            $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
            /*
                ->addPreInputCommand('-y') : overwrite existing file
            */
            $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addPreInputCommand('-framerate', $framerate)
            //->addPreInputCommand('-start_number', '00154')
            ->addPreInputCommand('-pattern_type', 'glob') 
            ->addCommand('-i', $path)
            ->addCommand('-vcodec', 'qtrle')
            ->addCommand('-vf', 'fps='. $fps)
            ->setOutputPath($target)
            ->execute();

            //\PHPVideoToolkit\Trace::vars($ffmpeg->getExecutedCommand(true));
        } catch(FfmpegProcessOutputException $e) {
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
        } catch(Exception $e) {
            /*echo '<h1>Error</h1>';
            \PHPVideoToolkit\Trace::vars($e->getMessage());
            echo '<h2>Exception</h2>';
            \PHPVideoToolkit\Trace::vars($e);
            */
            throw new InvalidArgumentException('Error when merging videos');
        }

        return $target;
    }

    /**
     * This method takes a mp4 source and transform it to Mpeg (with .ts as extension)
     *
     */
    public function transformMp4ToMpg($source, $target){
        try {
            $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
            $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
            ->addCommand('-i', $source)
            ->addCommand('-c', 'copy')
            ->addCommand('-bsf:v', 'h264_mp4toannexb')
            ->addCommand('-f', 'mpegts')
            ->setOutputPath($target)
            ->execute();
        } catch(FfmpegProcessOutputException $e) {
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
        } catch(Exception $e) {
            /*echo '<h1>Error</h1>';
            \PHPVideoToolkit\Trace::vars($e->getMessage());
            echo '<h2>Exception</h2>';
            \PHPVideoToolkit\Trace::vars($e);
            */
            throw new InvalidArgumentException('Error when merging videos');
        }

        return $target;
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
                // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
                $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
                $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
                    ->addCommand('-i', 'concat:' . implode('|', $videos))
                    ->addCommand('-c', 'copy')
                    ->addCommand('-bsf:a', 'aac_adtstoasc')
                    ->addCommand('-bufsize', '1835k')
                    ->addCommand('-fflags', 'genpts')
                    ->addCommand('-f', 'vob')
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

    public function mergeMp3ToMp4($audioSource, $videoSource, $target){
       // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
       $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
       
       $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
           ->addCommand('-i', $videoSource)
           ->addCommand('-i', $audioSource, true)
           ->setOutputPath($target)
           ->execute();
       return $target;
    }

    public function convertMp4ToOgv($videoSource, $target){
       // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
        $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
       
        $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
            ->addCommand('-i', $videoSource)
            ->setOutputPath($target)
            ->execute();
        
        return $target;
    }

    public function convertMovToMp4($videoSource, $target){
       // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
        $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
       
        $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addCommand('-i', $videoSource)
            ->addCommand('-vcodec', 'h264')
            ->addCommand('-acodec', 'aac')
            ->addCommand('-strict', '2')
            ->setOutputPath($target)
            ->execute();
        
        return $target;
    }

    public function mergeMp4($videoSource, $target){
       // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
        $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
       
        $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addCommand('-i', $videoSource)
            ->setOutputPath($target)
            ->execute();
        
        return $target;
    }

    /*
    *  this method takes an image (with alpha) or a mov video (the format to keep alpha channel) and overlay
    *  it on a video. 
    */
    public function overlayOnMp4($imageSource, $videoSource, $target){
        //ffmpeg -i gnd.mov -i test.png -filter_complex "[0:0][1:0]overlay=format=rgb[out]" -map [out] -vcodec qtrle test.mov
        // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
        $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
       
        $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addCommand('-i', $videoSource, true)
            ->addCommand('-i', $imageSource, true)
            ->addCommand('-filter_complex', '[0:0][1:0]overlay=format=rgb[out]')
            ->addCommand('-map', '[out]')
            ->addCommand('-vcodec', 'qtrle')
            ->setOutputPath($target)
            ->execute();

        //\PHPVideoToolkit\Trace::vars($ffmpeg->getExecutedCommand(true));
        
        return $target;
    }

    /**
     * This method will merge 2 MP4 videos
     * But it needs MP4Box as a dependency !!!
     * @param  array $videos
     * @return string
     */
    public function mergeMp4Videos($videoSource, $target)
    {
        exec('MP4Box -add $videoSource -brand mp42 $target');

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
