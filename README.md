# ImageConverter – Kirby ImageConversion Class

This plugin for [Kirby 2](http://getkirby.com) allows you to convert your images to rgb and resize them down for web use. 

It is based on the [Thumb](https://github.com/getkirby/toolkit/blob/master/lib/thumb.php) class of the Kirby Toolkit. 

**License**: [GNU GPL v3.0](http://opensource.org/licenses/GPL-3.0)

## Requirements

- Imagemagick
- php `exec` must be allowed

This plugin doesn't work with GDLib. 

## Installation

Use one of the alternatives below.

### 1. Kirby CLI

If you are using the Kirby CLI you can install this plugin by running the following commands in your shell:

```
$ cd path/to/kirby
$ kirby plugin:install madergrafisch/kirby-imageconverter
```


### 2. Clone or download

1. Clone or [download](https://github.com/madergrafisch/kirby-imageconverter/archive/master.zip)  this repository.
2. Unzip the archive if needed and rename the folder to `kirby-imageconverter`.
**Make sure that the plugin folder structure looks like this:**
```
site/plugins/kirby-imageconverter/
```

## Usage

Use kirby 2 [panel upload hook](http://getkirby.com/docs/panel/hooks) to process images on file upload.

```php
kirby()->hook('panel.file.upload', function($file) {

  if ($file->type() == 'image') {
    $image = new mgf\ImageConverter($file, array(
      'width' => 1024,
      'height' => 1024,
      'tosRGB' => true,
    ));
    $image->process();
  }

});
```

## Examples

Create a new ImageConverter Object by passing a Media Object and some params. 

```php
$image = new mgf\ImageConverter($image);
$image->process();
```

This uses the default options, like below. 

```php
$image = new mgf\ImageConverter($image, array(
  'width' => 1024,
  'height' => 1024,
  'tosRGB' => true,
));
$image->process();
```php

This converts your image to a maximum size of 1024x1024 px and converts its colorspace to sRGB.

```php
$image = new mgf\ImageConverter($image, array(
  'filename' => '{name}_resized.{extension}',
));
$image->process();
```

This creates a new image in the same destination, as the original image and adds the suffix '_resized' to the filename.

## Options

The class offers some options, most of them by the Kirby Thumb Class.

### Default Options
 
```php
'filename'   => '{name}.{extension}'
'url'        => null
'root'       => null
'quality'    => 100
'width'      => 960
'height'     => 960
'upscale'    => false
'tosRGB'     => false
'autoOrient' => false
```

## Credits

- [Christian Zehetner](https://github.com/seehat/) - Author
- [Bastian Allgeier](https://github.com/bastianallgeier) - Author of Kirby Thumbs plugin