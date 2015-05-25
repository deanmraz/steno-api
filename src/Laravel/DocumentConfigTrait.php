<?php namespace DMraz\StenoApi\Laravel;

use Illuminate\Support\Facades\Config;

trait DocumentConfigTrait
{
  public function loadConfig()
  {
    $directories = Config::get('steno-api::directories');

    foreach($directories as $directory)
    {
      $this->addDocuments($directory);
    }
  }
}