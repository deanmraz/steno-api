<?php namespace DMraz\StenoApi;

class DocumentParser
{
  protected $version;
  protected $base_parser_class = 'DMraz\\StenoApi\\StenoParser\\';
  protected $parser;

  public function load($path)
  {
    $this->parse(file_get_contents($path));
  }

  public function toArray()
  {
    return [
      'version' => $this->version,
      'api' => $this->getApi(),
      'resource' => $this->getResource(),
      'operations' => $this->getOperations(),
    ];
  }

  public function toJson($pretty = false)
  {
    if($pretty) return "<pre>".json_encode($this->toArray(),JSON_PRETTY_PRINT)."</pre>";
    else return json_encode($this->toArray());
  }

  public function parse($document)
  {
    $document = $this->cleanup($document);

    # segment document
    $segmented = $this->segmentDocument($document);

    # match first level parent child
    $match = $this->match($segmented);

    # match 2nd level child's parent child
    $sub_match = $this->subMatch($match);

    # get version
    $version = $this->getDocumentVersion($sub_match);

    # parse version
    $this->parseVersion($version, $sub_match);
  }

  protected function cleanup($document)
  {
    # standardize line breaks
    $document = str_replace(array("\r\n", "\r"), "\n", $document);

    # remove surrounding line breaks
    $document = trim($document, "\n");

    return $document;
  }

  protected function segmentDocument($document)
  {
    $segment = [];
    $lines = explode("\n", $document);
    foreach($lines as $line)
    {
      $trimmed = trim($line);
      if(!empty($trimmed)) $segment[] = $line;
    }
    return $segment;
  }

  protected function lineIsParent($line)
  {
    $substr = trim(substr($line,0,2));
    return $substr === '#' || $substr === '##';
  }

  protected function lineIsSubParent($line)
  {
    $substr = trim(substr($line,0,2));
    return $substr === '##';
  }

  protected function match($segment)
  {
    $matched = [];
    $last_key = null;
    foreach($segment as $line)
    {
      if($this->lineIsParent($line))
      {
        if(is_null($last_key)) $last_key = 0;
        else $last_key++;
        $matched[] = [
          'parent' => $line,
          'children' => []
        ];
      }
      else
      {
        $matched[$last_key]['children'][] = $line;
      }
    }
    return $matched;
  }

  protected function subMatch($matched)
  {
    $sub_match = [];
    $last_key = null;
    foreach($matched as $match)
    {
      if($this->lineIsSubParent($match['parent']))
      {
        $sub_match[$last_key]['children'][] = $match;
      }
      else
      {
        if(is_null($last_key)) $last_key = 0;
        else $last_key++;
        $sub_match[$last_key] = $match;
      }
    }
    return $sub_match;
  }

  /**
   * @return mixed
   */
  public function getVersion()
  {
    return $this->version;
  }

  protected function getDocumentVersion($document)
  {
    foreach($document as $section)
    {
      $line = isset($section['parent']) ? $section['parent'] : null;
      if($line && $this->isVersion($line))
      {
        $this->setVersion($section);
        break;
      }
    }
    if(empty($this->version)) throw new \Exception("Document Error - No version number provided");
    return $this->version;
  }

  protected function isVersion($line)
  {
    return strpos(strtolower($line),'version') !== false;
  }

  protected function setVersion($section)
  {
    preg_match('/\d+(?:\.\d+)+/', $section['parent'],$matches);
    $this->version = $matches[0];
  }

  protected function parseVersion($version, $document)
  {
    $converted = str_replace('.','_', $version);
    $class_name = $this->base_parser_class."Version_$converted";
    $this->parser = $parser = new $class_name;
    $parser->parse($document);
  }

  /**
   * @return mixed
   */
  public function getOperations()
  {
    return $this->parser->getOperations();
  }

  /**
   * @return mixed
   */
  public function getResource()
  {
    return $this->parser->getResource();
  }

  /**
   * @return mixed
   */
  public function getApi()
  {
    return $this->parser->getApi();
  }
}