<?php namespace DMraz\StenoApi\Laravel;

use DMraz\StenoApi\DocumentParser;

trait StenoApiTestTrait
{
  protected function getDocument()
  {
    $file = base_path($this->file);
    $document = new DocumentParser;
    $document->load($file);
    return $document;
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
  }

}

