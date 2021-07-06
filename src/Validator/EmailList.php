<?php
namespace PlaygroundCore\Validator;

use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;

class EmailList extends AbstractValidator
{
    const FORBIDDEN = 'FORBIDDEN';

    protected $options = [];

    protected $messageTemplates = array(
        self::FORBIDDEN => "This email is not allowed",
    );

    public function __construct($options = null)
    {
        $this->options = $options;
        parent::__construct($this->options);
    }

    public function isValid($value)
    {
        $this->setValue(strtolower($value));
        if (is_array($this->options) && in_array(strtolower($value), $this->options)) {
            return true;
        }

        $this->error(self::FORBIDDEN);

        return false;
    }
}
