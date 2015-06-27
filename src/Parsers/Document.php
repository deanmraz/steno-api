<?php
namespace DMraz\StenoApi\Parsers;

class Document
{
  public function parse($document)
  {
    $document = $this->cleanup($document);

    # get line collection
    $lines = $this->segmentLines($document);

  }

  protected function cleanup($document)
  {
    # standardize line breaks
    $document = str_replace(array("\r\n", "\r"), "\n", $document);

    # remove surrounding line breaks
    $document = trim($document, "\n");

    return $document;
  }

  protected function segmentLines($document)
  {
    $collection = new LineCollection;
    $lines = explode("\n", $document);
    foreach($lines as $key => $line)
    {
      $collection->push(new Line($line, $key));
    }
    return $collection;
  }

}