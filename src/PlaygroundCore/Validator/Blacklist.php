<?php
//filename : module/TutorialValidator/src/TutorialValidator/Validator/Special.php
namespace PlaygroundCore\Validator;
 
use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;
                     
class Blacklist extends AbstractValidator
{
    const FORBIDDEN = 'FORBIDDEN';

    protected $options = array(
        'file' => null,  // File containing the blacklist file
    );
 
    protected $messageTemplates = array(
        self::FORBIDDEN => "You're not allowed to use this word",
    );
     
    public function __construct($options = null)
    {
    	//die('in');
        if (is_string($options)) {
			$this->options = array('file' => str_replace('\\', '/', getcwd()) . $options);
        } elseif(is_array($options)){
            $this->options = array('file' => str_replace('\\', '/', getcwd()) . $options[0]);
        }
		parent::__construct($options);

    }

    /**
     * Returns the blacklist file path
     *
     * @return int
     */
    public function getFile()
    {
        return $this->options['file'];
    }

    /**
     * Sets the path to the blacklist file
     *
     * @param  string $path to the blacklist file
     * @return Blacklist Provides a fluent interface
     * @throws Exception\InvalidArgumentException When file is not found
     */
    public function setFile($file)
    {

        if (empty($file) || false === stream_resolve_include_path($file)) {
            throw new Exception\InvalidArgumentException('Invalid options to validator provided');
        }

        $this->options['file'] = $file;
        return $this;
    }
 
    public function isValid($value)
    {
        $this->setValue(strtolower($value));

		if(strpos(file_get_contents($this->getFile()), strtolower($value)) !== false) {
		    $this->error(self::FORBIDDEN);
           	return false;
		}
 
       	return true;
    }
}