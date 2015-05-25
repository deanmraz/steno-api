<?php namespace DMraz\StenoApi\Laravel;

use DMraz\StenoApi\DocumentLoaderTrait;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;

class DocumentServer
{
  use DocumentLoaderTrait;
  use DocumentConfigTrait;

  protected function getBaseUrl()
  {
    return "/".Config::get('steno-api::document_server.uri')."/";
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
    Route::get($this->getBaseUrl().$document->getApi()['name'], function() use ($document) {
      return $document->toJson(true);
    });
  }
}