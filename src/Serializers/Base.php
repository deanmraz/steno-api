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

  abstract protected function find($sections);
}