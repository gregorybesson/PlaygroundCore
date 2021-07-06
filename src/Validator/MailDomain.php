<?php
namespace PlaygroundCore\Validator;

use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;

class MailDomain extends AbstractValidator
{
    const FORBIDDEN = 'FORBIDDEN';

    protected $options = [];
 
    protected $messageTemplates = array(
        self::FORBIDDEN => "This domain is not allowed",
    );
     
    public function __construct($options = null)
    {
        $this->options = $options;
        parent::__construct($this->options);
    }
 
    public function isValid($value)
    {
        // get only the mail domain
        $domain = explode('@', $value);
        $domain = end($domain);
        $this->setValue(strtolower($domain));

        if (is_array($this->options) && in_array(strtolower($domain), $this->options)) {
            return true;
        }

        $this->error(self::FORBIDDEN);

        return false;
    }
}
