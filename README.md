# ImageConverter – Kirby ImageConversion Class

This plugin for [Kirby 2](http://getkirby.com) allows you to convert your images to rgb and resize them down for web use. 

It is based on the [Thumb](https://github.com/getkirby/toolkit/blob/master/lib/thumb.php) class of the Kirby Toolkit. 

**Version**: 1.0.0

**Author**: [@seehat](https://github.com/seehat/)

**License**: [GNU GPL v3.0](http://opensource.org/licenses/GPL-3.0)

## Installation

### Copy & Pasting

If not already existing, add a new `plugins` folder to your `site` directory. Then copy or link this repositories whole content in a new `imageconverter` folder there. Afterwards, your directory structure should look like this:

```
site/
  plugins/
    imageconverter/
      imageconverter.php
      sRGB.icc
```

### Git Submodule

If you are an advanced user and know your way around Git and you already use Git to manage you project, you can make updating this field extension to newer releases a breeze by adding it as a Git submodule.

```bash
$ cd your/project/root
$ git submodule add git@github.com:madergrafisch/kirby-imageconverter.git site/plugins/imageconverter
```

Updating all your Git submodules (eg. the Kirby core modules and any extensions added as submodules) to their latest version, all you need to do is to run these two Git commands.

```bash
$ cd your/project/root
$ git submodule foreach --recursive git checkout master
$ git submodule foreach --recursive git pull
```

## Usage

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
```

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