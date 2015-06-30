<?php

use DMraz\StenoApi\Serializers\Api;
use DMraz\StenoApi\Serializers\Resource;
use DMraz\StenoApi\Serializers\Rest;
use DMraz\StenoApi\Parsers\Section;
use Illuminate\Support\Collection;

class SerializersTest extends \PHPUnit_Framework_TestCase
{
  protected function createSections()
  {
    $section = new Section;
    $section->title = "Parent 1";

    $api_section = new Section;
    $api_section->key = "API";
    $api_section->value = "This is the api";

    $resource_section = new Section;
    $resource_section->key = "Resource";
    $resource_section->value = "ResourceName";
    $resource_section->addAttributeKeyValue("attribute1","value1");
    $resource_section->addAttributeKeyValue("key","value");

    $get = new Section;
    $get->key = "REST";
    $get->value = "GET /path/to/get/uri";

    $post = new Section;
    $post->key = "REST";
    $post->value = "POST /path/to/post/uri";

    $put = new Section;
    $put->key = "REST";
    $put->value = "PUT /path/to/put/uri";

    $delete = new Section;
    $delete->key = "REST";
    $delete->value = "DELETE /path/to/delete/uri";

    return new Collection([$section, $api_section, $resource_section, $get, $post, $put, $delete]);
  }

  public function testApi()
  {
    $collection = $this->createSections();
    $api = new Api($collection);
    $this->assertEquals("This is the api", $api->getName());
  }

  public function testResource()
  {
    $collection = $this->createSections();
    $resource = new Resource($collection);
    $this->assertEquals("ResourceName", $resource->getName());
    $this->assertEquals("value", $resource->key);
    $this->assertEquals("value1", $resource->attribute1);
  }

  public function testREST()
  {
    $collection = $this->createSections();
    $rest = new Rest($collection);

    $gets = $rest->getVerbUris('GET');
    $this->assertNotFalse(array_search('/path/to/get/uri', $gets));

    $posts = $rest->getVerbUris('POST');
    $this->assertNotFalse(array_search('/path/to/post/uri', $posts));

    $puts = $rest->getVerbUris('PUT');
    $this->assertNotFalse(array_search('/path/to/put/uri', $puts));

    $deletes = $rest->getVerbUris('DELETE');
    $this->assertNotFalse(array_search('/path/to/delete/uri', $deletes));
  }
}