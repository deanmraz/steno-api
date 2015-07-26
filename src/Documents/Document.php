<?php
namespace DMraz\StenoApi\Documents;

abstract class Document
{
  abstract public function getSerializers();

  public function create($sections)
  {
    $serializerNames = $this->getSerializers();
    $serializers = [];
    foreach($serializerNames as $serializerName) {
      $serializer = new $serializerName($sections);
      $serializers[$serializer->getSerializerName()] = $serializer;
    }
    return $serializers;
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
