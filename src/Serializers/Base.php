<?php
namespace DMraz\StenoApi\Serializers;

abstract class Base
{
  protected $section;

  public function __construct($sections)
  {
    $this->iterateSections($sections);
  }

  protected function iterateSections($sections)
  {
    foreach($sections as $section)
    {
      if($this->find($section)) {
        break;
      }
    }
  }

  public function getSerializerName()
  {
    $reflect = new \ReflectionClass(get_class($this));
    return strtolower($reflect->getShortName());
  }

  abstract protected function find($sections);
}