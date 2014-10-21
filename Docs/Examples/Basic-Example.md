Basic Example
=============

Attach the behavior to the model:

```php
class SomeModel extends AppModel {
	public $actsAs = array(
		'Imagine.Imagine'
	);
}
```

Now you can start manipulating images.

Flip an image vertical and crop it to 100x100px:

```php
$imageOperations = array(
	'flip' => array(
		'direction' => 'vertically'
	),
	'crop' => array(
		'height' => 100,
		'width' => 100
	),
);
$this->Image->processImage(
	APP . 'files' . DS . 'image.jpg',
	APP . 'files' . DS . 'modifiedImage.jpg',
	array(),
	$imageOperations
);
```

Create a thumbnail with a max height of 600px and a max width of 200px:

```php
$imageOperations = array(
	'thumbnail' => array(
		'height' => 600,
		'width' => 200
	),
);
$this->Image->processImage(
	APP . 'files' . DS . 'image.jpg',
	APP . 'files' . DS . 'modifiedImage.jpg',
	array(),
	$imageOperations
);
```


