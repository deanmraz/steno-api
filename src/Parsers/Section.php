<?php
namespace DMraz\StenoApi\Parsers;

class Section
{
  public $number;
  public $after;
  public $depth;

  public $key;
  public $value;
  public $title;
  public $children = [];

  protected $description;
  protected $attributes;
  protected $attribute_types = [];

  protected function checkAttributeHasBeenSet($key)
  {
    if(isset($this->attributes[$key])) {
      throw new \Exception("Usage Error - attribute: `$key` has already been set");
    }
  }

  protected function setAttributeType($key, $type)
  {
    if(isset($this->attribute_types[$key])) {
      if($this->attribute_types[$key] !== $type) {
        throw new \Exception("Usage Error - cannot change attribute->$key's type to `$type` from `{$this->attribute_types[$key]}`");
      }
    } else {
      $this->attribute_types[$key] = $type;
    }
  }

  public function addAttributeKeyValue($key,$value)
  {
    $this->checkAttributeHasBeenSet($key);
    $this->attributes[$key] = $value;
    $this->setAttributeType($key, 'key_value');
  }

  public function addAttributeList($key)
  {
    $this->checkAttributeHasBeenSet($key);
    $this->attributes[$key] = [];
  }

  public function addAttributeListItem($attribute, $value)
  {
    $this->attributes[$attribute][] = $value;
    $this->setAttributeType($attribute, 'list_sequential');

  }

  public function addAttributeListKeyValue($key, $value, $attribute)
  {
    $this->attributes[$attribute][$key] = $value;
    $this->setAttributeType($attribute, 'list_key_value');
  }

  public function get($key)
  {
    return array_get($this->attributes, $key);
  }

  public function setDescription($text)
  {
    $this->description = $text;
  }

  public function getDescription()
  {
    return $this->description ? : null;
  }

}