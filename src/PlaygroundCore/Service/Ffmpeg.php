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

    public function convertToMp3($source, $target){
        //ffmpeg -i son_origine.avi -vn -ar 44100 -ac 2 -ab 192 -f mp3 son_final.mp3
       // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
        $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
       
        $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addCommand('-i', $source)
            ->addCommand('-vn')
            ->addCommand('-ar', '44100')
            ->addCommand('-ac', '2')
            ->addCommand('-ab', '192')
            ->addCommand('-f', 'mp3')
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
    *  this method takes an image (with alpha) or a mov video (the format to keep alpha channel) and overlay this layer
    *  on a background video. 
    */
    public function overlayOnMp4($source, $layer, $target, $x='main_w/2-overlay_w/2', $y='main_h/2-overlay_h/2'){
        //ffmpeg -i gnd.mov -i test.png -filter_complex "[0:0][1:0]overlay=format=rgb[out]" -map [out] -vcodec qtrle test.mov
        // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
        $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
       
        $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addCommand('-i', $source, true)
            ->addCommand('-i', $layer, true)
            ->addCommand('-filter_complex', '[0:0][1:0]overlay=x='.$x.':y='.$y.':format=rgb[out]')
            ->addCommand('-map', '[out]')
            ->addCommand('-vcodec', 'qtrle')
            ->setOutputPath($target)
            ->execute();

        //\PHPVideoToolkit\Trace::vars($ffmpeg->getExecutedCommand(true));
        
        return $target;
    }

    /*
    *  this method takes an image (with alpha) or a mov video (the format to keep alpha channel) and overlay this layer
    *  on a background video. 
    */
    public function overlayTextOnMp4($source, $font, $fontSize, $fontColor, $message, $x, $y, $target){
        // ffmpeg -i sub_video3.mp4 -vf drawtext="fontfile=/usr/share/fonts/truetype/ttf-dejavu/DejaVuSerif.ttf: \
        // text='Text to write is this one, overlaid':fontsize=20:fontcolor=red:x=100:y=100" with_text.mp4
        
        // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
        $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);

        $text = "fontfile=$font:text='". $message."':fontsize=". $fontSize .":fontcolor=" . $fontColor . ":x=".$x.":y=".$y;
       
        $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addCommand('-i', $source)
            ->addCommand('-vf', 'drawtext='.$text)
            ->setOutputPath($target)
            ->execute();

        \PHPVideoToolkit\Trace::vars($ffmpeg->getExecutedCommand(true));
        
        return $target;
    }

    /*
    *  this method extracts an image form a video at the $time second in the video. 
    */
    public function extractImage($source, $time, $target){
        //ffmpeg -i webcam_2012-03-18_00_33_58.mp4 -r 0.1 -t 20 image%3d.jpg
        // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
        $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
       
        $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addCommand('-i', $source)
            ->addCommand('-r', '0.1')
            ->addCommand('-t', $time)
            ->setOutputPath($target)
            ->execute();

        //\PHPVideoToolkit\Trace::vars($ffmpeg->getExecutedCommand(true));
        
        return $target;
    }

    /*
    *  this method takes an image (with alpha) or a mov video (the format to keep alpha channel) and overlay
    *  it on a video. 
    */
    // public function overlayComplexOnMp4($imageSource, $videoSource, $target){
    //     //ffmpeg -i bg.mp4 -i fg.mkv -filter_complex "[0:v][1:v]overlay=enable='between=(t,10,20)':x=720+t*28:y=t*10[out]" -map "[out]" output.mkv
    //     // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
    //     $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
       
    //     $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
    //         ->addPreInputCommand('-y')
    //         ->addCommand('-i', $videoSource, true)
    //         ->addCommand('-i', $imageSource, true)
    //         ->addCommand('-filter_complex', "'[0:v][1:v]overlay=enable='between=(t,10,20)':x=720+t*28:y=t*10[out]'")
    //         ->addCommand('-map', '[out]')
    //         ->addCommand('-vcodec', 'qtrle')
    //         ->setOutputPath($target)
    //         ->execute();

    //     //\PHPVideoToolkit\Trace::vars($ffmpeg->getExecutedCommand(true));
        
    //     return $target;
    // }


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
