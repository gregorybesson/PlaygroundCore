<?php
/**
 * Copyright (c) 2012 Jurian Sluiman.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of the
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     SlmGoogleAnalytics
 * @author      Jurian Sluiman <jurian@juriansluiman.nl> updated by GrG <gregory.besson@playground.gg>
 * @copyright   2012 Jurian Sluiman.
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://juriansluiman.nl
 */
namespace PlaygroundCore\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Helper\HeadScript;
use PlaygroundCore\Analytics\Tracker;
use Laminas\Stdlib\RequestInterface;
use PlaygroundCore\Exception\RuntimeException;

class GoogleAnalytics extends AbstractHelper
{
    /**
     * @var Tracker
     */
    protected $tracker;

    /**
     * @var string
     */
    protected $container = 'InlineScript';

    /**
     * @var bool
     */
    protected $rendered = false;
    protected $request;

    public function __construct(Tracker $tracker, RequestInterface $request)
    {
        $this->tracker = $tracker;
        $this->request = $request;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function __invoke()
    {
        // Do not render the GA twice
        if ($this->rendered) {
            return;
        }

        // Do not render when tracker is disabled
        $tracker = $this->tracker;
        if (!$tracker->enabled()) {
            return;
        }

        // We return if we are in a console request
        if ((get_class($this->request) == 'Laminas\Console\Request')) {
            return;
        }

        // We need to be sure $container->appendScript() can be called
        $container = $this->view->plugin($this->getContainer());
        if (!$container instanceof HeadScript) {
            throw new RuntimeException(sprintf(
                'Container %s does not extend HeadScript view helper',
                $this->getContainer()
            ));
        }

        $script  = "var _gaq = _gaq || [];\n";
        $script .= sprintf("_gaq.push(['_setAccount', '%s']);\n", $tracker->getId());

        if (null !== ($customVars = $tracker->customVars())) {
            foreach ($customVars as $customVar) {
                $script .= sprintf(
                    "_gaq.push(['_setCustomVar', %s, '%s', '%s', %s]);\n",
                    $customVar->getId(),
                    $customVar->getName(),
                    $customVar->getValue(),
                    $customVar->getOptScope() ?: ''
                );
            }
        }

        if ($tracker->getDomainName()) {
            $script .= sprintf("_gaq.push(['_setDomainName', '%s']);\n", $tracker->getDomainName());
        }

        if ($tracker->getAllowLinker()) {
            $script .= "_gaq.push(['_setAllowLinker', true]);\n";
        } else {
            $script .= "_gaq.push(['_setAllowLinker', false]);\n";
        }

        if ($tracker->getAllowHash()) {
            $script .= "_gaq.push(['_setAllowHash', true]);\n";
        } else {
            $script .= "_gaq.push(['_setAllowHash', false]);\n";
        }

        if ($tracker->getAnonymizeIp()) {
            $script .= "_gaq.push(['_gat._anonymizeIp']);\n";
        }

        if ($tracker->enabledPageTracking()) {
            $script .= "_gaq.push(['_trackPageview']);\n";
        }

        if ($tracker->enabledPageLoadTime()) {
            $script .= "_gaq.push(['_trackPageLoadTime']);\n";
        }

        if (null !== ($events = $tracker->events())) {
            foreach ($events as $event) {
                $script .= sprintf(
                    "_gaq.push(['_trackEvent', '%s', '%s', '%s', '%s']);\n",
                    $event->getCategory(),
                    $event->getAction(),
                    $event->getLabel() ?: '',
                    $event->getValue() ?: ''
                );
            }
        }

        if (null !== ($transactions = $tracker->transactions())) {
            foreach ($transactions as $transaction) {
                $script .= sprintf(
                    "_gaq.push(['_addTrans', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);\n",
                    $transaction->getId(),
                    $transaction->getAffiliation() ?: '',
                    $transaction->getTotal(),
                    $transaction->getTax() ?: '',
                    $transaction->getShipping() ?: '',
                    $transaction->getCity() ?: '',
                    $transaction->getState() ?: '',
                    $transaction->getCountry() ?: ''
                );

                if (null !== ($items = $transaction->items())) {
                    foreach ($items as $item) {
                        $script .= sprintf(
                            "_gaq.push(['_addItem', '%s', '%s', '%s', '%s', '%s', '%s']);\n",
                            $transaction->getId(),
                            $item->getSku() ?: '',
                            $item->getProduct() ?: '',
                            $item->getCategory() ?: '',
                            $item->getPrice(),
                            $item->getQuantity()
                        );
                    }
                }
            }

            $script .= "_gaq.push(['_trackTrans']);";
        }

        $script .= <<<SCRIPT
(function () {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();\n
SCRIPT;

        // Addthis analytics integration code for google analytics
        $script .= sprintf("var addthis_config = {
data_ga_property: '%s',
data_track_clickback: true
};\n", $tracker->getId());

        $container->appendScript($script);

        // Mark this GA as rendered
        $this->rendered = true;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }
}
