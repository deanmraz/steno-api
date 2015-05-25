<?php namespace DMraz\StenoApi\Laravel;

use DMraz\StenoApi\DocumentParser;

trait StenoApiTestTrait
{
  protected $document;

  protected function getDocument()
  {
    if(empty($this->document))
    {
      $file = base_path($this->file);
      $document = new DocumentParser;
      $document->load($file);
      $this->document = $document;
    }
    return $this->document;
  }

  protected function tryAllOperations()
  {
    $document = $this->getDocument();

    foreach($document->getOperations() as $operation)
    {
      $this->tryOperation($operation);
    }
  }

  protected function tryOperation($operation)
  {
    $uri = $operation['uri'];
    $method = $operation['method'];
    $crawler = $this->client->request($method, $uri);
    $this->assertTrue($this->client->getResponse()->isOk(), "$method $uri is okay");
    $this->checkContent($this->client->getResponse()->getContent());
  }

  protected function checkContent($content)
  {
    $result = json_decode($content, true);
    $document = $this->getDocument();

    //check resource payload
    $name = $document->getResource()['name'];
    $name = str_plural(strtolower($name));
    $this->assertArrayHasKey($name, $result);

    //check attributes
    $attributes = $document->getResource()['attributes'];
    foreach($attributes as $attribute)
    {
      $this->assertArrayHasKey($attribute['name'], $result[$name][0], "Checking $name for attribute {$attribute['name']}");
    }
  }

}

