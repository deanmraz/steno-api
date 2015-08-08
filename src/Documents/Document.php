<?php
namespace DMraz\StenoApi\Documents;

abstract class Document
{
  protected $serializers = [];

  abstract public function getSerializers();

  public function create($sections)
  {
    $serializerNames = $this->getSerializers();
    foreach($serializerNames as $serializerName) {
      $serializer = new $serializerName($sections);
      $this->serializers[$serializer->getSerializerName()] = $serializer;
    }
    return $this->serializers;
  }

  public function getSerializer($key)
  {
    return isset($this->serializers[$key]) ? $this->serializers[$key] : null;
  }

  public function __get($key)
  {
    return $this->getSerializer($key);
  }

}
