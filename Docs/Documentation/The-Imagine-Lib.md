# Imagine Lib #

The lib provides convenience access to some of the most commonly used image operations.

```php
App::uses('ImagineLib', 'Imagine.Lib');

$this->Imagine = new ImagineLib();
$file = TMP . 'in.jpg';
$Image = $this->Imagine->open($file);

// Operations
$this->Imagine->widen($Image, array('size' => 100));
$this->Imagine->rotate($Image, array('degree' => 90));
...
$file = TMP . 'out.jpg';
$this->Imagine->save($Image, $file);
```