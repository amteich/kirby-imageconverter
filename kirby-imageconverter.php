<?php 

namespace mgf;

use Obj;
use Exception;
use Str;
use Media;
use F;

class ImageConverter extends Obj {

  const ERROR_INVALID_IMAGE  = 0;

  static public $defaults = array(
    'destination'=> '{name}.{extension}',
    'url'        => null,
    'root'       => null,
    'quality'    => 100,
    'blur'       => false,
    'blurpx'     => 10,
    'width'      => null,
    'height'     => null,
    'upscale'    => false,
    'crop'       => false,
    'grayscale'  => false,
    'tosRGB'     => false,
    'autoOrient' => false,
    'interlace'  => false,
  );

  public $source      = null;
  public $result      = null;
  public $destination = null;
  public $options     = array();
  public $error       = null;

  /**
   * Constructor
   *
   * @param mixed $source
   * @param array $params
   */
  public function __construct($source, $params = array()) {

    $this->source  = $this->result = is_a($source, 'Media') ? $source : new Media($source);

    // set source root as default
    static::$defaults['url'] = $this->source->url();
    static::$defaults['root'] = $this->source->dir();

    $this->options = array_merge(static::$defaults, $params);

    $this->destination = new Obj();
    $this->destination->filename = str::template($this->options['destination'], array(
      'extension'    => $this->source->extension(),
      'name'         => $this->source->name(),
      'filename'     => $this->source->filename(),
      'safeName'     => f::safeName($this->source->name()),
      'safeFilename' => f::safeName($this->source->name()) . '.' . $this->extension(),
      'width'        => $this->options['width'],
      'height'       => $this->options['height'],
    ));

    $this->destination->url  = $this->options['url'] . '/' . $this->destination->filename;
    $this->destination->root = $this->options['root'] . DS . $this->destination->filename;

    // check for a valid image
    if(!$this->source->exists() or $this->source->type() != 'image') {
      throw new Exception('The given image is invalid', static::ERROR_INVALID_IMAGE);
    }

  }

  public static function convert ($source, $params = array()) {
    $image = new static($source, $params);
    return $image->process();
  }

  public function process () {
    // create the image
    $this->create();

    // check if processing the image failed
    if(!file_exists($this->destination->root)) return;

    // create the result object
    $this->result = new Media($this->destination->root, $this->destination->url);

    return $this->result;
  }

  protected function create() {

    $command = array();
    $command[] = isset($this->options['bin']) ? $this->options['bin'] : 'convert';
    $command[] = escapeshellarg($this->source->root());

    // if image has profile -> convert to sRGB
    // results in better colors, because most browsers
    // don't read colorprofiles and assume sRGB instead
    if($this->options['tosRGB']) {
      $command[] = '-profile ' . __DIR__ . DS . 'sRGB.icc';
    }

    // strip color profile after conversion,
    // because sRGB is assumed by most browsers
    $command[] = '-strip';

    if($this->options['interlace']) {
      $command[] = '-interlace line';
    }

    if($this->source->extension() === 'gif') {
      $command[] = '-coalesce';
    }

    if($this->options['grayscale']) {
      $command[] = '-colorspace gray';
    }

    if($this->options['autoOrient']) {
      $command[] = '-auto-orient';
    }

    $command[] = '-resize';

    if($this->options['crop']) {
      $command[] = $this->options['width'] . 'x' . $this->options['height'] . '^';
      $command[] = '-gravity Center -crop ' . $this->options['width'] . 'x' . $this->options['height'] . '+0+0';
    } else {
      $dimensions = clone $this->source->dimensions();
      $dimensions->fitWidthAndHeight($this->options['width'], $this->options['height'], $this->options['upscale']);
      $command[] = $dimensions->width() . 'x' . $dimensions->height() . '!';
    }

    $command[] = '-quality ' . $this->options['quality'];

    if($this->options['blur']) {
      $command[] = '-blur 0x' . $this->options['blurpx'];
    }

    $command[] = '-limit thread 1';
    $command[] = escapeshellarg($this->destination->root());

    exec(implode(' ', $command));
  }

}
