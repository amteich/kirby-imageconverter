<?php 

class ImageConverter extends Obj {

  const ERROR_INVALID_IMAGE  = 0;

  static public $defaults = array(
    'filename'   => '{name}.{extension}',
    'url'        => null,
    'root'       => null,
    'quality'    => 100,
    'width'      => 960,
    'height'     => 960,
    'upscale'    => false,
    'tosRGB'     => false,
    'autoOrient' => false,
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
    $this->destination->filename = str::template($this->options['filename'], array(
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

    if($this->options['tosRGB']) {
      $command[] = '-profile ' . __DIR__ . DS . 'sRGB.icc';
    }

    $command[] = '-resize';

    $dimensions = clone $this->source->dimensions();
    $dimensions->fitWidthAndHeight($this->options['width'], $this->options['height'], $this->options['upscale']);
    $command[] = $dimensions->width() . 'x' . $dimensions->height() . '!';

    if($this->options['autoOrient']) {
      $command[] = '-auto-orient';
    }
    
    $command[] = '-quality ' . $this->options['quality'];

    $command[] = escapeshellarg($this->destination->root());

    $command = implode(' ', $command);

    exec($command . ' 2>&1', $output, $status);
    $success = $status === 0;
    if (!$success) {
      throw new Exception("Error processing image with command: " . $command . PHP_EOL . implode(PHP_EOL, $output));
    }
  }



}