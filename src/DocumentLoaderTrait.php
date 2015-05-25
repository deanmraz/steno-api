<?php namespace DMraz\StenoApi;

trait DocumentLoaderTrait
{
  protected $documents;

  public function addDocuments($path)
  {
    if(file_exists($path)) {
      $files = scandir($path);
      foreach($files as $file)
      {
        if($file !== '.' && $file !== '..') $this->addFile(realpath($path.$file));
      }
    }
  }

  public function addFile($file_path)
  {
    if(file_exists($file_path)) {
      $document = new DocumentParser;
      $document->load($file_path);
      $this->documents[] = $document;
    }
  }
}