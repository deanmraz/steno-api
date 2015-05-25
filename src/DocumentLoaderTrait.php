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
        if($file !== '.' && $file !== '..') $this->addFile(realpath("$path/$file"));
      }
    }
    else throw new \Exception("Directory doesn't exist $path");
  }

  public function addFile($file_path)
  {
    if(file_exists($file_path)) {
      $document = new DocumentParser;
      $document->load($file_path);
      $this->documents[] = $document;
    }
    else throw new \Exception("File doesn't exist $file_path");
  }
}