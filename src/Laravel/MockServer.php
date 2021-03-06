<?php namespace DMraz\StenoApi\Laravel;

use DMraz\StenoApi\DocumentLoaderTrait;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;

class MockServer
{
  use DocumentLoaderTrait;
  use DocumentConfigTrait;

  protected $allowed_methods =  ['GET','POST','PUT','DELETE'];

  protected function getBaseUrl()
  {
    return "/".Config::get('steno-api::mock_server.uri');
  }

  public function start()
  {
    if(!empty($this->documents))
    {
      foreach($this->documents as $document)
      {
        $this->createApi($document);
      }
    }
  }

  public function createApi($document)
  {
    $operations = $document->getOperations();
    if(!empty($operations))
    {
      foreach($operations as $operation)
      {
        $uri = $operation['uri'];
        $method = $operation['method'];
        $allowed = $this->allowed_methods;

        if(!empty($method) && array_search($method, $allowed)!==false)
        {
          Route::$method($this->getBaseUrl().$uri, function() use ($operation) {
            $example = $operation['examples'][0]['Response'];
            $response =  Response::make($example['Body'],$example['StatusCode']);
            if(isset($example['ContentType'])) $response->header('Content-Type', $example['ContentType']);
            return $response;
          });
        }
      }
    }
  }
}