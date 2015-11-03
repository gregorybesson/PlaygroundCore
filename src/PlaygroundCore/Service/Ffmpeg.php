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
     *  ffmpeg -i in.mp4 -c copy -bsf:v h264_mp4toannexb -f mpegts out.ts
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
     * This method takes a mp4 source and transform it to Mpeg (with .ts as extension)
     *  ffmpeg -i in.mov -vcodec h264 -pix_fmt yuv420p -f mpegts out.ts
     */
    public function transformMovToMpg($source, $target){
        try {
            $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
            $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
            ->addCommand('-i', $source)
            ->addCommand('-vcodec', 'h264')
            ->addCommand('-pix_fmt', 'yuv420p')
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
                    ->addPreInputCommand('-y')
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

        //\PHPVideoToolkit\Trace::vars($ffmpeg->getExecutedCommand(true));

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
    public function overlayOnMp4($source, $layer, $target){
        //ffmpeg -i gnd.mov -i test.png -filter_complex "[0:0][1:0]overlay=format=rgb[out]" -map [out] -vcodec qtrle test.mov

        if(!is_array($layer)){
            $layer = array($layer);
        }
        $overlay = '';
        $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
       
        $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addCommand('-i', $source, true);

        foreach($layer as $k=>$l){
            $ffmpeg->addCommand('-i', $l, true);
            $overlay .= 'overlay=format=rgb';
            if($k<count($layer)-1) 
                $overlay .= ',';
        }

        $ffmpeg->addCommand('-filter_complex', $overlay.'[out]')
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
    public function addWavToMp4($video, $sound, $target){
        //ffmpeg -i video.mp4 -i audio.wav -c:v copy -c:a aac -strict experimental output.mp4
        // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.

        $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
       
        $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addCommand('-i', $video, true)
            ->addCommand('-i', $sound, true)
            ->addCommand('-c:v', 'copy')
            ->addCommand('-c:a', 'aac')
            ->addCommand('-strict', 'experimental')
            ->setOutputPath($target)
            ->execute();

        //\PHPVideoToolkit\Trace::vars($ffmpeg->getExecutedCommand(true));
        
        return $target;
    }

    /*
    *  this method takes an audio file and increase its level
    *  on a background video. 
    */
    public function increaseVolumeSound($source, $level, $target){
        // ffmpeg -i title.wav -af "volume=4" titleok.wav
        // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
        $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
       
        $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addCommand('-i', $source, true)
            ->addCommand('-af', 'volume='.$level)
            ->setOutputPath($target)
            ->execute();

        //\PHPVideoToolkit\Trace::vars($ffmpeg->getExecutedCommand(true));
        
        return $target;
    }

    /*
    *  this method creates an audio file of $duration seconds with no sound
    *  on a background video. 
    */
    public function createNullSound($duration=1, $target){
        // ffmpeg -y -filter_complex 'aevalsrc=0:d=1.6' silence.wav
        // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
        $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
       
        $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addCommand('-filter_complex', 'aevalsrc=0:d='.$duration)
            ->setOutputPath($target)
            ->execute();

        //\PHPVideoToolkit\Trace::vars($ffmpeg->getExecutedCommand(true));
        
        return $target;
    }

    /**
    * This method concatenate an array of sounds
    */
    public function concatenateSounds($sounds = array(), $target){
        // ffmpeg -y -i silence1-1.wav -i titleok.wav -i silence1-2.wav -filter_complex '[0:0][1:0][2:0]concat=n=3:v=0:a=1[out]' -map '[out]' sound1.wav
        
        if(!is_array($sounds)){
            $sounds = array($sounds);
        }

        $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
        $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y');

        $concat = '';
        foreach($sounds as $k=>$s){
            $ffmpeg->addCommand('-i', $s, true);
            $concat .= '['.$k.':0]';
        }
        $concat .= 'concat=n='. count($sounds) .':v=0:a=1[out]';

        $ffmpeg->addCommand('-filter_complex', $concat)
            ->addCommand('-map', '[out]')
            ->setOutputPath($target)
            ->execute();

        //\PHPVideoToolkit\Trace::vars($ffmpeg->getExecutedCommand(true));

        return $target;
    }

    /**
    * This method merge an array of sounds
    */
    public function mergeSounds($sounds = array(), $target){
        // ffmpeg -i bearnaise.wav -i sound1.wav -i sound2.wav -filter_complex "[0:a][1:a][2:a]amerge=inputs=3[aout]" -map "[aout]" -ac 2 sound.wav
        
        if(!is_array($sounds)){
            $sounds = array($sounds);
        }

        $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
        $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y');

        $merge = '';
        foreach($sounds as $k=>$s){
            $ffmpeg->addCommand('-i', $s, true);
            $merge .= '['.$k.':a]';
        }
        $merge .= 'amerge=inputs='. count($sounds) .'[aout]';

        $ffmpeg->addCommand('-filter_complex', $merge)
            ->addCommand('-map', '[aout]')
            ->addCommand('-ac', '2')
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

    /**
    *  this method extracts an image form a video at the $time second in the video. 
    *  ffmpeg -ss 00:00:04 -i video.mp4 -vframes 1 out.png
    */
    public function extractImage($source, $target, $start = '00:00:01', $frames = 1 ){

        // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
        $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
       
        $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y');

        if(!empty($start))
            $ffmpeg->addPreInputCommand('-ss', $start);

        $ffmpeg->addCommand('-i', $source)
            ->addCommand('-vframes', $frames)
            ->setOutputPath($target)
            ->execute();

        //\PHPVideoToolkit\Trace::vars($ffmpeg->getExecutedCommand(true));
        
        return $target;
    }

    /**
    *  this method splits a video into n chunks defined by the frames array.
    *  $frames = array(array(0, 12), array(13, 110), array(111, 200), array(201, 268), array(269, 363), array(364, 390), array(391, 417), array(418, 437), array(438, 553), array(554, 600));
    */
    public function splitVideo($source, $frames = array(), $target){
        //ffmpeg -i quickns.mov -an -vf "select=between(n\,110\,200),setpts=PTS-STARTPTS" grg.mov
        
        // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
        $this->getServiceManager()->setShared('playgroundcore_phpvideotoolkit', false);
        
        $i=1;
        foreach($frames as $frame){

            //echo $frame[0] . " -> " .$frame[1] . "<br/>";
            $ffmpeg = $this->getServiceManager()->get('playgroundcore_phpvideotoolkit')
                ->addPreInputCommand('-y')
                ->addCommand('-i', $source)
                ->addCommand('-an')
                ->addCommand('-vf', 'select=between(n\,' . $frame[0] . '\,' . $frame[1] . '),setpts=PTS-STARTPTS')
                ->setOutputPath($target . sprintf('s%02d', $i) . '.mov')
                ->execute();
            $i++;
        }
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
