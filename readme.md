# Imagine Plugin for CakePHP #

## Setup ##

You need to configure a salt for Imagine security functions.

	Configure::write('Imagine.salt', 'your-salt-string-here');

We do not use Security.salt on purpose because we do not want to use the same salt here for security reasons.

## Imagine Helper ##

The helper will generate image urls with named params to get thumbnails or whatever else operation is wanted and a hashes the url.

The hash can be checked using the Imagine Component to avoid that people try to bring your page down by incrementing the size of a requested thumbnail to generate thousands of images on your server.

	$url = $this->Imagine->url(
		array(
			'controller' => 'images',
			'action' => 'display',
			1),
		array(
			'thumbnail' => array(
				'width' => 200,
				'height' => 150)));
	echo $this->Html->image($url);

### Special note for high traffic sites ###

You should *not* generate images on the fly on high traffic sites, it might get your server locked up because of the many many requests!

The first request will hit your server and start generating the image while others try to do that at the same time causing the site become locked up in the worst case.

It is better to generate the needed versions after an image was uploaded and if other versions are needed later, generate them by a shell script.

## Imagine Component ##

To be documented

## Imagine Behavior ##

To be documented

## Caching and Storage ##

This plugin *does not* take care of how you store the images or how you cache them but it will offer you some helping methods for caching images based on a hash or a unique string.

This is a design decision that was made because everyone likes to implement the file storage a little different. So it is up to you how you store the generated images.