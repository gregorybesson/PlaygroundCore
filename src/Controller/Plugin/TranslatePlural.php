<?php

namespace PlaygroundCore\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\Mvc\I18n\Translator;

final class TranslatePlural extends AbstractPlugin
{
    /**
     * @var Translator
     */
    private $translator;
    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }
    /**
     * Translate a plural message
     *
     * @param  string $singular
     * @param  string $plural
     * @param  int    $number
     * @param  string $textDomain
     * @param  string $locale
     * @return string
     */
    public function __invoke($singular, $plural, $number, $textDomain = 'default', $locale = null)
    {
        return $this->translator->translatePlural($singular, $plural, $number, $textDomain, $locale);
    }
    public function __call($method, $args)
    {
        return call_user_func_array(array($this->translator, $method), $args);
    }
}
