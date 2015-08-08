<?php

use DMraz\StenoApi\Parsers\Document;

class ExampleRestTest extends PHPUnit_Framework_TestCase
{
  public function testRest()
  {
    $markdown = file_get_contents(__DIR__."/Examples/Restful.md");
    $parser = new Document;
    $document = $parser->parse($markdown, "DMraz\\StenoApi\\Documents\\DocumentHttp");

    //get api
    $this->assertEquals('Restful',$document->api->getName());

    //check resource
    $this->assertEquals('restfulResource', $document->resource->getName());
    $this->assertEquals('string', $document->resource->id);
    $this->assertEquals('string', $document->resource->name);
    $this->assertEquals('email', $document->resource->email);
    $this->assertEquals('text', $document->resource->content);

    //restful get apis
    foreach($document->http->getFirst()->children as $example)
    {
      $this->assertEquals('All', $example->value);
      $body = $example->get('Response.Body');
      $this->assertNotNull($body);
      $json = json_decode($body, true);
      $this->assertArrayHasKey('restfulResources', $json);
      $this->assertEquals('1', $json['restfulResources'][0]['id']);
      $this->assertEquals('rest name', $json['restfulResources'][0]['name']);
      $this->assertEquals('rest@email.com', $json['restfulResources'][0]['email']);
      $this->assertArrayHasKey('content', $json['restfulResources'][0]);
    }
  }
}