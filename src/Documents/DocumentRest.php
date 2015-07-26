<?php
namespace DMraz\StenoApi\Documents;

class DocumentRest extends Document
{
  protected $serializers;

  public function getSerializers()
  {
    return [
      'DMraz\StenoApi\Serializers\Api',
      'DMraz\StenoApi\Serializers\Resource',
      'DMraz\StenoApi\Serializers\Rest',
    ];
  }
}
