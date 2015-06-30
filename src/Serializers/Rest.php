<?php
namespace DMraz\StenoApi\Serializers;

class Rest extends Base
{
  protected $get = [];
  protected $post = [];
  protected $put = [];
  protected $delete = [];

  protected function find($section)
  {
    if(strtoupper($section->key) === 'REST') {
      $this->segmentVerb($section);
    }
    return false;
  }

  protected function segmentVerb($section)
  {
    $text = explode(' ',$section->value);

    if(strtoupper($text[0]) === 'GET') {
      $this->get[$text[1]] = $section;
    } else  if(strtoupper($text[0]) === 'POST') {
      $this->post[$text[1]] = $section;
    } else  if(strtoupper($text[0]) === 'PUT') {
      $this->put[$text[1]] = $section;
    } else  if(strtoupper($text[0]) === 'DELETE') {
      $this->delete[$text[1]] = $section;
    }
    
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

  public function getVerbUris($verb)
  {
    return array_keys($this->{strtolower($verb)});
  }
}