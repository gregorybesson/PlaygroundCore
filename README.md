PlaygroundCore
=========

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/a27d7563-4f8d-4c79-bde4-df8148e14344/big.png)](https://insight.sensiolabs.com/projects/a27d7563-4f8d-4c79-bde4-df8148e14344)

[![Develop Branch Build Status](https://travis-ci.org/gregorybesson/PlaygroundCore.svg)](http://travis-ci.org/gregorybesson/PlaygroundCore)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/gregorybesson/PlaygroundCore/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/gregorybesson/PlaygroundCore/)
[![Coverage Status](https://coveralls.io/repos/gregorybesson/PlaygroundCore/badge.svg?branch=develop&service=github)](https://coveralls.io/github/gregorybesson/PlaygroundCore?branch=develop)

[![Latest Stable Version](https://poser.pugx.org/playground/core/v/stable)](https://packagist.org/packages/playground/core) [![Total Downloads](https://poser.pugx.org/playground/core/downloads)](https://packagist.org/packages/playground/core) [![Latest Unstable Version](https://poser.pugx.org/playground/core/v/unstable)](https://packagist.org/packages/playground/core) [![License](https://poser.pugx.org/playground/core/license)](https://packagist.org/packages/playground/core)


This library contains the following features :

- Google Analytics : Tagging Google Analytics (fork of Jurian Sluiman project : https://github.com/juriansluiman/SlmGoogleAnalytics)
- Facebook Tags
- Twilio
- CKEditor : Wysiwyg editor (fork of https://github.com/Celtico/QuCKEditor from Celtico)
- ELFinder : Added to CKEditor to manage assets on server. (fork of https://github.com/Celtico/QuElFinder).
- Cron : Cron Engine (fork of https://github.com/heartsentwined/zf2-cron with deep refactoring so that it's now based on ZF2 events)
- ShortenUrl : URL Shortener based on Bit.ly
- MailService : Templating mails.
- Slugify : Transform text into slug (useful for creating url)
- Core layout : The base for creating the structure layout of a website.

Each feature is explained in the wiki : https://github.com/gregorybesson/PlaygroundCore/wiki



# Migration Laminas
1. Màj bootstrap de /tests avec
```
  $config = ArrayUtils::merge($baseConfig, $testConfig);

  $smConfig = new ServiceManagerConfig($config);
  $serviceManager = new ServiceManager();
  $smConfig->configureServiceManager($serviceManager);

  $serviceManager->setService('ApplicationConfig', $config);
  $serviceManager->get('ModuleManager')->loadModules();
```

1. Remplacer
```
public function setUp()
```
par
```
protected function setUp(): void
```

3. Remplacer
```
public function tearDown()
```
par
```
protected function tearDown(): void
```

4. Remplacer
```
\PHPUnit_Framework_TestCase
```
par
```
\PHPUnit\Framework\TestCase
```

5. Les annotations PHPUNIT
6. Remplacer
```
'MvcTranslator'                      => 'Zend\Mvc\I18n\TranslatorServiceFactory',
```
par
```
'MvcTranslator'                      => 'Zend\I18n\Translator\TranslatorServiceFactory',
```
