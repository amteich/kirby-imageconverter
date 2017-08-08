# ImageConverter – Kirby ImageConversion Class

This plugin for [Kirby 2](http://getkirby.com) allows you to convert your images to rgb and resize them down for web use. 

It is based on the [Thumb](https://github.com/getkirby/toolkit/blob/master/lib/thumb.php) class of the Kirby Toolkit. 

**Version**: 1.0.0

**Author**: [@seehat](https://github.com/seehat/)

**License**: [GNU GPL v3.0](http://opensource.org/licenses/GPL-3.0)

## Installation

You must have a working installation of ImageMagick and php 'exec' must be allowed. This plugin doesn't work with GDLib. 

### Copy & Pasting

If not already existing, add a new `plugins` folder to your `site` directory. Then copy or link this repositories whole content in a new `imageconverter` folder there. Afterwards, your directory structure should look like this:

```
site/
  plugins/
    imageconverter/
      imageconverter.php
      sRGB.icc
```

## Usage

Use kirby 2 [panel upload hook](http://getkirby.com/docs/panel/hooks) to process images on file upload.

```php
kirby()->hook('panel.file.upload', function($file) {

  if ($file->type() == 'image') {
    $image = new ImageConverter($file, array(
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
$image = new ImageConverter($image);
$image->process();
```

This uses the default options, like below. 

```php
$image = new ImageConverter($image, array(
  'width' => 1024,
  'height' => 1024,
  'tosRGB' => true,
));
$image->process();
```php

This converts your image to a maximum size of 1024x1024 px and converts its colorspace to sRGB.

```php
$image = new ImageConverter($image, array(
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