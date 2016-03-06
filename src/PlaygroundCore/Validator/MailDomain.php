<?php
//filename : module/TutorialValidator/src/TutorialValidator/Validator/Special.php
namespace PlaygroundCore\Validator;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

class MailDomain extends AbstractValidator
{
    const FORBIDDEN = 'FORBIDDEN';

    protected $options = array(
        'file' => null,  // File containing the authorized domains
    );
 
    protected $messageTemplates = array(
        self::FORBIDDEN => "This domain is not allowed",
    );
     
    public function __construct($options = null)
    {
        if (is_string($options)) {
            $this->options = array('file' => str_replace('\\', '/', getcwd()) . '/' . ltrim($options, '/'));
        } elseif (is_array($options)) {
            $this->options = array('file' => str_replace('\\', '/', getcwd()) . '/' . ltrim(reset($options), '/'));
        }

        parent::__construct($this->options);
    }

    /**
     * Returns the file path
     *
     * @return int
     */
    public function getFile()
    {
        return $this->options['file'];
    }

    /**
     * Sets the path to the file
     *
     * @param  string $path to the file
     * @return MailDomain Provides a fluent interface
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
        // get only the mail domain
        $domain = explode('@', $value);
        $domain = end($domain);
        $this->setValue(strtolower($value));
        $domains = file($this->getFile(), FILE_IGNORE_NEW_LINES);

        if (is_array($domains) && in_array(strtolower($domain), $domains)) {
            return true;
        }

        $this->error(self::FORBIDDEN);

        return false;
    }
}
