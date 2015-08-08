<?php
namespace DMraz\StenoApi\Serializers;

class Api extends Base
{
  protected function find($section)
  {
    if(strtoupper($section->key) === 'API') {
      $this->section = $section;
      return true;
    }
    return false;
  }

  public function getName()
  {
    return $this->section->value;
  }
}