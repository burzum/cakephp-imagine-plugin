Imagine Behavior
================

The behavior interacts with the component and will process a given image file with a set of operations that should be applied to it. See ImagineBehavior::processImage().

```php
class SomeModel extends AppModel {
	public $actsAs = array(
		'Imagine.Imagine'
	);
}
```

## Imagine instance ##

The plain  Imagine instance is available through the model. Get it by calling

```php
$this->imagineObject();
```

or directly through the behavior

```php
$this->Behaviors->Imagine->Imagine
```