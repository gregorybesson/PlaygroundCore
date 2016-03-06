<?php
namespace PlaygroundCore\Mail\Service;

use Zend\ServiceManager\ServiceManager;
use Zend\Mail\Message as MailMessage;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part;
use Zend\ServiceManager\ServiceLocatorInterface;

class Message
{
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
     *
     * @var \Zend\View\Renderer\RendererInterface
     */
    protected $renderer;

    /**
     *
     * @var \Zend\Mail\Transport\TransportInterface
     */
    protected $transport;

    /**
     * Return a HTML message ready to be sent
     *
     * @param array|string $from
     *            A string containing the sender e-mail address, or if array with keys email and name
     * @param array|string $to
     *            An array containing the recipients of the mail
     * @param string $subject
     *            Subject of the mail
     * @param string|\Zend\View\Model\ModelInterface $nameOrModel
     *            Either the template to use, or a ViewModel
     * @param null|array $values
     *            Values to use when the template is rendered
     * @return Message
     */
    public function createHtmlMessage($from, $to, $subject, $nameOrModel, $values = array())
    {
        if (is_string($from)) {
            $from = array('email' => $from, 'name' => $from);
        }
        $renderer = $this->getRenderer();
        $content = $renderer->render($nameOrModel, $values);
        $resolver = $this->serviceLocator->get('Zend\View\Resolver\TemplatePathStack');
        // check if plain text email template exist
        if ($resolver->resolve($nameOrModel . '-plain')) {
            $contentText = $renderer->render($nameOrModel . '-plain', $values);
        } else {
            $contentText = '';
        }

        $mail = new MailMessage();
        $mail->addTo($to);
        $mail->addFrom($from['email'], $from['name']);
        $mail->setSubject($subject);
        $text              = new Part($contentText);
        $text->type        = Mime::TYPE_TEXT;
        $text->encoding    = Mime::ENCODING_QUOTEDPRINTABLE;
        $text->disposition = Mime::DISPOSITION_INLINE;
        $text->charset     = 'UTF-8';
        $html              = new Part($content);
        $html->type        = Mime::TYPE_HTML;
        $html->encoding    = Mime::ENCODING_QUOTEDPRINTABLE;
        $html->disposition = Mime::DISPOSITION_INLINE;
        $html->charset     = 'UTF-8';
        $bodyMessage     = new MimeMessage();

        $multiPartContentMessage = new MimeMessage();
        $multiPartContentMessage->addPart($text);
        $multiPartContentMessage->addPart($html);
        $multiPartContentMimePart           = new Part($multiPartContentMessage->generateMessage());
        $multiPartContentMimePart->charset  = 'UTF-8';
        $multiPartContentMimePart->type     = 'multipart/alternative';
        $multiPartContentMimePart->boundary = $multiPartContentMessage->getMime()->boundary();
        $bodyMessage->addPart($multiPartContentMimePart);
        $mail->setBody($bodyMessage);
        $mail->setEncoding('UTF-8');

        return $mail;
    }

    /**
     * Return a text message ready to be sent
     *
     * @param array|string $from
     *            A string containing the sender e-mail address, or if array with keys email and name
     * @param array|string $to
     *            An array containing the recipients of the mail
     * @param string $subject
     *            Subject of the mail
     * @param string|\Zend\View\Model\ModelInterface $nameOrModel
     *            Either the template to use, or a ViewModel
     * @param null|array $values
     *            Values to use when the template is rendered
     * @return Message
     */
    public function createTextMessage($from, $to, $subject, $nameOrModel, $values = array())
    {
        $renderer = $this->getRenderer();
        $content = $renderer->render($nameOrModel, $values);

        return $this->getDefaultMessage($from, 'utf-8', $to, $subject, $content);
    }

    /**
     * Send the message
     *
     * @param Message $message
     */
    public function send(MailMessage $message)
    {
        $this->getTransport()
            ->send($message);
    }

    /**
     * Get the renderer
     *
     * @return \Zend\View\Renderer\RendererInterface
     */
    protected function getRenderer()
    {
        if ($this->renderer === null) {
            $this->renderer = $this->serviceLocator->get('ViewRenderer');
        }

        return $this->renderer;
    }

    /**
     * Get the transport
     *
     * @return \Zend\Mail\Transport\TransportInterface
     */
    protected function getTransport()
    {
        if ($this->transport === null) {
            $this->transport = $this->serviceLocator->get('playgroundcore_transport');
        }

        return $this->transport;
    }

    /**
     *
     * @return Message
     */
    protected function getDefaultMessage($from, $encoding, $to, $subject, $body)
    {
        if (is_string($from)) {
            $from = array('email' => $from, 'name' => $from);
        }

        $message = new MailMessage();
        $message->setFrom($from['email'], $from['name'])
            ->setEncoding($encoding)
            ->setSubject($subject)
            ->setBody($body)
            ->setTo($to);

        $message->getHeaders()->get('content-type')->setType('multipart/alternative');

        return $message;
    }
}
