<?php
namespace PlaygroundCore\Service;

use Laminas\ServiceManager\ServiceManager;
use Laminas\EventManager\EventManagerAwareTrait;
use PHPVideoToolkit\FfmpegProcessOutputException;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * main class
 * To trace an execute command: \PHPVideoToolkit\Trace::vars($ffmpeg->getExecutedCommand(true));
 *
 *
 * On exception:
 *     $ffmpeg = $e->getProcess();
 *     execute command : \PHPVideoToolkit\Trace::vars($e);
 *     FFmpeg Process Messages: \PHPVideoToolkit\Trace::vars($ffmpeg->getMessages());
 *     Buffer Output: \PHPVideoToolkit\Trace::vars($ffmpeg->getBuffer(true));
 *
 */
class Ffmpeg
{
    use EventManagerAwareTrait;

    /**
     * @var ModuleOptions
     */
    protected $options;

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
     * This method create images from a video.
     * ffmpeg -i input.mov output_%03d.png
     */
    public function createImagesFromVideos($source, $target = 'step-%03d.jpg')
    {
        try {
            $this->serviceLocator->setShared('playgroundcore_phpvideotoolkit', false);

            $this->serviceLocator->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addCommand('-i', $source)
            ->setOutputPath($target)
            ->execute();
        } catch (FfmpegProcessOutputException $e) {
            throw new \PHPVideoToolkit\InvalidArgumentException('Error when merging videos');
        } catch (\PHPVideoToolkit\Exception $e) {
            throw new \PHPVideoToolkit\InvalidArgumentException('Error when merging videos');
        }

        return $target;
    }

    /**
     * This method create a video from still images (jpg or png).
     * ffmpeg -framerate 1/5 -i etape%01d.jpg -c:v libx264 -vf "fps=25,format=yuv420p" out.mp4
     */
    public function createVideoFromImages($path = 'data/etape*.jpg', $target = false, $framerate = '25', $fps = 25)
    {
        try {
            // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
            $this->serviceLocator->setShared('playgroundcore_phpvideotoolkit', false);

            $this->serviceLocator->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addPreInputCommand('-framerate', $framerate)
            ->addPreInputCommand('-pattern_type', 'glob')
            ->addCommand('-i', $path)
            ->addCommand('-c:v', 'libx264')
            ->addCommand('-vf', 'fps='. $fps)
            ->addCommand('-pix_fmt', 'yuv420p')
            ->setOutputPath($target)
            ->execute();
        } catch (FfmpegProcessOutputException $e) {
            throw new \PHPVideoToolkit\InvalidArgumentException('Error when merging videos');
        } catch (\PHPVideoToolkit\Exception $e) {
            throw new \PHPVideoToolkit\InvalidArgumentException('Error when merging videos');
        }

        return $target;
    }

    /**
     * This method create a video from still images (jpg or png).
     * ffmpeg -framerate 1/5 -i etape%01d.jpg -c:v libx264 -vf "fps=25,format=yuv420p" out.mp4
     * ffmpeg -framerate 1/5 -i etape%01d.jpg '-vcodec' 'qtrle' out.mov
     */
    public function createMovFromAlphaImages($path = 'data/etape*.jpg', $target = false, $framerate = '25', $fps = 25)
    {
        try {
            // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
            $this->serviceLocator->setShared('playgroundcore_phpvideotoolkit', false);

            $this->serviceLocator->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addPreInputCommand('-framerate', $framerate)
            //->addPreInputCommand('-start_number', '00154')
            ->addPreInputCommand('-pattern_type', 'glob')
            ->addCommand('-i', $path)
            ->addCommand('-vcodec', 'qtrle')
            ->addCommand('-vf', 'fps='. $fps)
            ->setOutputPath($target)
            ->execute();
        } catch (FfmpegProcessOutputException $e) {
            throw new \PHPVideoToolkit\InvalidArgumentException('Error when merging videos');
        } catch (\PHPVideoToolkit\Exception $e) {
            throw new \PHPVideoToolkit\InvalidArgumentException('Error when merging videos');
        }

        return $target;
    }

    /**
     * This method takes a mp4 source and transform it to Mpeg (with .ts as extension)
     *  ffmpeg -i in.mp4 -c copy -bsf:v h264_mp4toannexb -f mpegts out.ts
     */
    public function transformMp4ToMpg($source, $target)
    {
        try {
            $this->serviceLocator->setShared('playgroundcore_phpvideotoolkit', false);
            $this->serviceLocator->get('playgroundcore_phpvideotoolkit')
            ->addCommand('-i', $source)
            ->addCommand('-c', 'copy')
            ->addCommand('-bsf:v', 'h264_mp4toannexb')
            ->addCommand('-f', 'mpegts')
            ->setOutputPath($target)
            ->execute();
        } catch (FfmpegProcessOutputException $e) {
            throw new \PHPVideoToolkit\InvalidArgumentException('Error when merging videos');
        } catch (\PHPVideoToolkit\Exception $e) {
            throw new \PHPVideoToolkit\InvalidArgumentException('Error when merging videos');
        }

        return $target;
    }

    /**
     * This method takes a mp4 source and transform it to Mpeg (with .ts as extension)
     *  ffmpeg -i in.mov -vcodec h264 -pix_fmt yuv420p -f mpegts out.ts
     */
    public function transformMovToMpg($source, $target)
    {
        try {
            $this->serviceLocator->setShared('playgroundcore_phpvideotoolkit', false);
            $this->serviceLocator->get('playgroundcore_phpvideotoolkit')
            ->addCommand('-i', $source)
            ->addCommand('-vcodec', 'h264')
            ->addCommand('-pix_fmt', 'yuv420p')
            ->addCommand('-f', 'mpegts')
            ->setOutputPath($target)
            ->execute();
        } catch (FfmpegProcessOutputException $e) {
            throw new \PHPVideoToolkit\InvalidArgumentException('Error when merging videos');
        } catch (\PHPVideoToolkit\Exception $e) {
            throw new \PHPVideoToolkit\InvalidArgumentException('Error when merging videos');
        }

        return $target;
    }
    
    /**
     * This method will merge videos in .mpg format with exactly the same codec and codec parameters :
     * http://trac.ffmpeg.org/wiki/Concatenate
     * @param  array $videos
     * @return string
     */
    public function mergeMpgVideos($videos = false, $target = false)
    {
        if (is_array($videos)) {
            try {
                $this->serviceLocator->setShared('playgroundcore_phpvideotoolkit', false);
                $this->serviceLocator->get('playgroundcore_phpvideotoolkit')
                    ->addPreInputCommand('-y')
                    ->addCommand('-i', 'concat:' . implode('|', $videos))
                    ->addCommand('-c', 'copy')
                    ->addCommand('-bsf:a', 'aac_adtstoasc')
                    ->addCommand('-bufsize', '1835k')
                    ->addCommand('-fflags', 'genpts')
                    ->addCommand('-f', 'vob')
                    ->setOutputPath($target)
                    ->execute();
            } catch (FfmpegProcessOutputException $e) {
                throw new \PHPVideoToolkit\InvalidArgumentException('Error when merging videos');
            } catch (\PHPVideoToolkit\Exception $e) {
                throw new \PHPVideoToolkit\InvalidArgumentException('Error when merging videos');
            }
        }

        return $target;
    }

    public function mergeMp3ToMp4($audioSource, $videoSource, $target)
    {
        $this->serviceLocator->setShared('playgroundcore_phpvideotoolkit', false);
       
        $this->serviceLocator->get('playgroundcore_phpvideotoolkit')
           ->addCommand('-i', $videoSource)
           ->addCommand('-i', $audioSource, true)
           ->setOutputPath($target)
           ->execute();
        return $target;
    }

    public function convertMp4ToOgv($videoSource, $target)
    {
        $this->serviceLocator->setShared('playgroundcore_phpvideotoolkit', false);
       
        $this->serviceLocator->get('playgroundcore_phpvideotoolkit')
            ->addCommand('-i', $videoSource)
            ->setOutputPath($target)
            ->execute();
        
        return $target;
    }

    /**
     * ffmpeg -i sound.avi -vn -ar 44100 -ac 2 -ab 192 -f mp3 out.mp3
     */
    public function convertToMp3($source, $target)
    {
        $this->serviceLocator->setShared('playgroundcore_phpvideotoolkit', false);
       
        $this->serviceLocator->get('playgroundcore_phpvideotoolkit')
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

    public function convertMovToMp4($videoSource, $target)
    {
        $this->serviceLocator->setShared('playgroundcore_phpvideotoolkit', false);
       
        $this->serviceLocator->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addCommand('-i', $videoSource)
            ->addCommand('-vcodec', 'h264')
            ->addCommand('-acodec', 'aac')
            ->addCommand('-strict', '2')
            ->addCommand('-pix_fmt', 'yuv420p')
            ->addCommand('-movflags', '+faststart')
            ->setOutputPath($target)
            ->execute();
        
        return $target;
    }

    public function mergeMp4($videoSource, $target)
    {
        // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
        $this->serviceLocator->setShared('playgroundcore_phpvideotoolkit', false);
       
        $this->serviceLocator->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addCommand('-i', $videoSource)
            ->setOutputPath($target)
            ->execute();
        
        return $target;
    }

    /*
    *  this method takes an image (with alpha) or a mov video (the format to keep alpha channel) and overlay this layer
    *  on a background video.
    *  ffmpeg -i in.mov -i in.png -filter_complex "[0:0][1:0]overlay=format=rgb[out]" -map [out] -vcodec qtrle out.mov
    */
    public function overlayOnMp4($source, $layer, $target)
    {
        if (!is_array($layer)) {
            $layer = array($layer);
        }
        $overlay = '';
        $this->serviceLocator->setShared('playgroundcore_phpvideotoolkit', false);
       
        $ffmpeg = $this->serviceLocator->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addCommand('-i', $source, true);

        foreach ($layer as $k => $l) {
            $ffmpeg->addCommand('-i', $l, true);
            $overlay .= 'overlay=format=rgb';
            if ($k<count($layer)-1) {
                $overlay .= ',';
            }
        }

        $ffmpeg->addCommand('-filter_complex', $overlay.'[out]')
            ->addCommand('-map', '[out]')
            ->addCommand('-vcodec', 'qtrle')
            ->setOutputPath($target)
            ->execute();

        return $target;
    }
    
    /*
    *  this method takes an image (with alpha) or a mov video (the format to keep alpha channel) and overlay this layer
    *  on a background video. 
    *  ffmpeg -i video.mp4 -i audio.wav -c:v copy -c:a aac -strict experimental output.mp4
    */
    public function addWavToMp4($video, $sound, $target)
    {
        $this->serviceLocator->setShared('playgroundcore_phpvideotoolkit', false);
       
        $this->serviceLocator->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addCommand('-i', $video, true)
            ->addCommand('-i', $sound, true)
            ->addCommand('-c:v', 'copy')
            ->addCommand('-c:a', 'aac')
            ->addCommand('-strict', 'experimental')
            ->setOutputPath($target)
            ->execute();

        return $target;
    }

    /*
    *  this method takes an audio file and increase its level
    *  on a background video.
    *
    * ffmpeg -i title.wav -af "volume=4" titleok.wav
    */
    public function increaseVolumeSound($source, $level, $target)
    {
        $this->serviceLocator->setShared('playgroundcore_phpvideotoolkit', false);
       
        $this->serviceLocator->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addCommand('-i', $source, true)
            ->addCommand('-af', 'volume='.$level)
            ->setOutputPath($target)
            ->execute();

        return $target;
    }

    /*
    *  this method creates an audio file of $duration seconds with no sound
    *  on a background video.
    *  ffmpeg -y -filter_complex 'aevalsrc=0:d=1.6' silence.wav
    */
    public function createNullSound($duration, $target)
    {
        if (empty($duration)) {
            $duration = 1;
        }

        $this->serviceLocator->setShared('playgroundcore_phpvideotoolkit', false);
       
        $this->serviceLocator->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addCommand('-filter_complex', 'aevalsrc=0:d='.$duration)
            ->setOutputPath($target)
            ->execute();

        return $target;
    }

    /**
    * This method concatenate an array of sounds
    * ffmpeg -y -i in-1.wav -i in-2.wav -i in-3.wav
    * -filter_complex '[0:0][1:0][2:0]concat=n=3:v=0:a=1[out]' -map '[out]' out.wav
    */
    public function concatenateSounds($sounds, $target)
    {
        if (empty($sounds)) {
            $sounds = array();
        }
        if (!is_array($sounds)) {
            $sounds = array($sounds);
        }

        $this->serviceLocator->setShared('playgroundcore_phpvideotoolkit', false);
        $ffmpeg = $this->serviceLocator->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y');

        $concat = '';
        foreach ($sounds as $k => $s) {
            $ffmpeg->addCommand('-i', $s, true);
            $concat .= '['.$k.':0]';
        }
        $concat .= 'concat=n='. count($sounds) .':v=0:a=1[out]';

        $ffmpeg->addCommand('-filter_complex', $concat)
            ->addCommand('-map', '[out]')
            ->setOutputPath($target)
            ->execute();

        return $target;
    }

    /**
    * This method merge an array of sounds
    * ffmpeg -i in-1.wav -i in-2.wav -i in-3.wav
    * -filter_complex "[0:a][1:a][2:a]amerge=inputs=3[aout]" -map "[aout]" -ac 2 out.wav
    */
    public function mergeSounds($sounds, $target)
    {
        if (empty($sounds)) {
            $sounds = array();
        }
        if (!is_array($sounds)) {
            $sounds = array($sounds);
        }

        $this->serviceLocator->setShared('playgroundcore_phpvideotoolkit', false);
        $ffmpeg = $this->serviceLocator->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y');

        $merge = '';
        foreach ($sounds as $k => $s) {
            $ffmpeg->addCommand('-i', $s, true);
            $merge .= '['.$k.':a]';
        }
        $merge .= 'amerge=inputs='. count($sounds) .'[aout]';

        $ffmpeg->addCommand('-filter_complex', $merge)
            ->addCommand('-map', '[aout]')
            ->addCommand('-ac', '2')
            ->setOutputPath($target)
            ->execute();

        return $target;
    }

    /*
    *  this method takes an image (with alpha) or a mov video (the format to keep alpha channel) and overlay this layer
    *  on a background video.
    *  ffmpeg -i sub_video3.mp4 -vf drawtext="fontfile=/usr/share/fonts/truetype/ttf-dejavu/DejaVuSerif.ttf:
    *  text='Text to write is this one, overlaid':fontsize=20:fontcolor=red:x=100:y=100" with_text.mp4
    */
    public function overlayTextOnMp4($source, $font, $fontSize, $fontColor, $message, $x, $y, $target)
    {
        $this->serviceLocator->setShared('playgroundcore_phpvideotoolkit', false);

        $text = "fontfile=$font:text='". $message."':fontsize=".
            $fontSize .":fontcolor=" . $fontColor . ":x=".$x.":y=".$y;
       
        $this->serviceLocator->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y')
            ->addCommand('-i', $source)
            ->addCommand('-vf', 'drawtext='.$text)
            ->setOutputPath($target)
            ->execute();
        
        return $target;
    }

    /**
    *  this method extracts an image form a video at the $time second in the video.
    *  ffmpeg -ss 00:00:04 -i video.mp4 -vframes 1 out.png
    */
    public function extractImage($source, $target, $start = '00:00:01', $frames = 1)
    {
        $this->serviceLocator->setShared('playgroundcore_phpvideotoolkit', false);
       
        $ffmpeg = $this->serviceLocator->get('playgroundcore_phpvideotoolkit')
            ->addPreInputCommand('-y');

        if (!empty($start)) {
            $ffmpeg->addPreInputCommand('-ss', $start);
        }

        $ffmpeg->addCommand('-i', $source)
            ->addCommand('-vframes', $frames)
            ->setOutputPath($target)
            ->execute();

        return $target;
    }

    /**
    *  this method splits a video into n chunks defined by the frames array.
    *  $frames = array(array(0, 12), array(13, 110), array(111, 200));
    *  ffmpeg -i quickns.mov -an -vf "select=between(n\,110\,200),setpts=PTS-STARTPTS" grg.mov
    */
    public function splitVideo($source, $frames, $target)
    {
        if (empty($frames)) {
            $frames = array();
        }
        $this->serviceLocator->setShared('playgroundcore_phpvideotoolkit', false);
        
        $i=1;
        foreach ($frames as $frame) {
            $this->serviceLocator->get('playgroundcore_phpvideotoolkit')
                ->addPreInputCommand('-y')
                ->addCommand('-i', $source)
                ->addCommand('-an')
                ->addCommand('-vf', 'select=between(n\,' . $frame[0] . '\,' . $frame[1] . '),setpts=PTS-STARTPTS')
                ->setOutputPath($target . sprintf('s%02d', $i) . '.mov')
                ->execute();
            $i++;
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
            $this->setOptions($this->serviceLocator->get('playgroundcore_module_options'));
        }

        return $this->options;
    }
}
