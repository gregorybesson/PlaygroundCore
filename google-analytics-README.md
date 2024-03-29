SlmGoogleAnalytics
===
Version 0.1.0 Created by Jurian Sluiman

Usage
---
As tracking for Google Analytics is done with javascript, a view helper is
available to generate the required code based on some configuration. The
generated code is pushed into a `Laminas\View\Helper\HeadScript` helper, by default
the `Laminas\View\Helper\InlineScript` is used, but this can be modified into
`HeadScript` or any other helper extending the `HeadScript` helper class.

The `SlmGoogleAnalytics\Analytics\Tracker` is aliased to `google-analytics` in
the Service Manager configuration. This object is used to configure the Google
Analytics tracking. You can access this object inside a controller using the locator:

```php
public function fooAction ()
{
    $ga = $this->getServiceLocator()->get('google-analytics');
}
```

You can disable the tracking completely:

```php
$ga->setEnableTracking(false);
```

If you want to track events and/or ecommerce transactions, but no page tracking,
it can be turned off too:

```php
$ga->setEnablePageTracking(false);
```

To track an event, you must instantiate a `SlmGoogleAnalytics\Analytics\Event`
and add it to the tracker:

```php
$event = new SlmGoogleAnalytics\Analytics\Event;
$event->setCategory('Videos');
$event->setAction('Play');
$event->setLabel('Gone With the Wind');  // optionally
$event->setValue(5);                     // optionally

$ga->addEvent($event);
```

To track a transaction, you should use the
`SlmGoogleAnalytics\Analytics\Ecommerce\Transaction` and add one or more
`SlmGoogleAnalytics\Analytics\Ecommerce\Item` objects.

```php
$transaction = new SlmGoogleAnalytics\Analytics\Ecommerce\Transaction;
$transaction->setId('1234');      // order ID
$transaction->setTotal('28.28');  // total

$item = new SlmGoogleAnalytics\Analytics\Ecommerce\Item;
$item->setPrice('11.99');         // unit price
$item->setQuantity('2');          // quantity

$transaction->addItem($item);

$ga->addTransaction($transaction);
```

The `Transaction` and `Item` have accessors and mutators for every property
Google is able to track (like `getTax()`, `getShipping()` and `getSku()`) but
left out in this example for the sake of clarity.
