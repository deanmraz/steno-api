<?php
namespace DMraz\StenoApi\Serializers;

class Resource extends Base
{
  protected function find($section)
  {
    if(strtoupper($section->key) === 'RESOURCE') {
      $this->section = $section;
      return true;
    }
    return false;
  }

  public function getName()
  {
    return $this->section->value;
  }

  public function getAttribute($key)
  {
    return $this->section->get($key);
  }

  public function __get($key)
  {
    return $this->getAttribute($key);
  }
}