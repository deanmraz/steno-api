<?php
namespace DMraz\StenoApi\Parsers;

use Illuminate\Support\Collection;

class Document
{
  protected $lines;
  protected $sections;
  protected $nested;
  protected $serializerNames;
  protected $serializers;

  public function parse($document, $type = null)
  {
    $document = $this->cleanup($document);

    # get line collection
    $this->lines = $lines = $this->segmentLines($document);

    # section the lines
    $this->sections = $sections = $this->segmentSections($lines);

    # nested sections
    $this->nested = $nested = $this->nestSections($sections);

    # create document object
    return $this->createDocument($this->nested, $type);
  }

  public function createDocument($sections, $type)
  {
    $document = new $type;
    $document->create($sections);
    return $document;
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
    $collection = new Collection;
    $lines = explode("\n", $document);
    foreach($lines as $key => $line)
    {
      $collection->push(new Line($line, $key));
    }
    return $collection;
  }

  protected function segmentSections(Collection $lineCollection)
  {
    $sections = new Collection;
    $lines = $lineCollection->all();
    $lastSection = null;
    $lastListAttribute = null;
    $codeBlockStarted = false;
    $codeBlockText = "";
    foreach($lines as $key => $line)
    {
      if($line->type === 'code_block' && !$codeBlockStarted) {
        $codeBlockStarted = "continue";
        continue;
      } else if ($line->type === 'code_block' && $codeBlockStarted) {
        $codeBlockStarted = "stop";
        $lastSection->set($lastListAttribute, $codeBlockText);

        if(strpos($lastListAttribute,".")!==false) {
          $lastAttrs = explode(".",$lastListAttribute);
          $lastListAttribute = $lastAttrs[0];
        } else {
          $lastListAttribute = null;
        }
      }

      if(!$codeBlockStarted) {
        $method = "segment".studly_case($line->type)."Line";
        $result = $this->$method($line, $lastSection, $lastListAttribute);
      } else if($codeBlockStarted === 'continue') {
        $codeBlockText .= $line->original;
        $result = $lastListAttribute;
      }else if($codeBlockStarted === 'stop') {
        $codeBlockText = "";
        $result = $lastListAttribute;
      }


      //set section
      if(is_object($result)) {
        $sections->push($result);
        $lastSection = $sections->last();
        $lastListAttribute = null;
      // set last list attribute
      }
      else if(!empty($result)) {
        $lastListAttribute = $result;
      } else {
        $lastListAttribute = null;
      }
    }
    return $sections;
  }

  /**
   * @param $line Line
   * @return Section
   */
  protected function segmentParentLine($line)
  {
    $section = new Section;
    $section->depth = strlen($line->leads);
    $section->key = $line->key;
    $section->value = $line->value;
    $section->title = $line->text;
    return $section;
  }

  protected function segmentJsonLine($line, $section, $lastListAttribute)
  {
      $section->continueAttributeKeyValueString($lastListAttribute, $line->json);
      return $lastListAttribute;
  }

  /**
   * @param $line
   * @param $section Section
   */
  protected function segmentTextLine($line, $section, $lastListAttribute)
  {
    if($lastListAttribute) {
      $section->continueAttributeKeyValueString($lastListAttribute, $line->text);
      return $lastListAttribute;
    }
    else {
      $section->setDescription($line->text);
      return null;
    }
  }

  /**
   * @param $line
   * @param $section Section
   */
  protected function segmentListLine($line, $section, $attribute)
  {
    $section->addAttributeList($line->key, $attribute);
    return $attribute ? "$attribute.{$line->key}" : $line->key;
  }

  /**
   * @param $line
   * @param $section Section
   */
  protected function segmentListItemLine($line, $section, $attribute)
  {
    $section->addAttributeListItem($attribute, $line->text);
    return $attribute;
  }

  /**
   * @param $line
   * @param $section Section
   */
  protected function segmentListKeyValueLine($line, $section, $attribute)
  {
    $section->addAttributeListKeyValue($line->key, $line->value, $attribute);
    return $attribute;
  }

  /**
   * @param $line
   * @param $section Section
   */
  protected function segmentKeyValueLine($line, $section)
  {
    $section->addAttributeKeyValue($line->key, $line->value);
  }

  /**
   * @param $line
   * @param $section Section
   */
  protected function segmentCodeBlockLine($line, $section, $attribute)
  {

  }

  /**
   * @param $line
   * @param $section Section
   */
  protected function segmentNoneLine($line, $section)
  {

  }

  protected function findNextParent($depth,Collection $sections, $after)
  {
    $next = $sections->slice($after);

    return $next->first(function($key, $item) use($depth) {
      return $item->depth === $depth;
    });
  }

  protected function nestSections(Collection $sections)
  {
    $nested = new Collection;
    $reverse = $sections->reverse();
    foreach($reverse->all() as $key => $section)
    {
      // if 3 depth merge to next one
      if($section->depth === 3) {
        $parent = $this->findNextParent(2,$reverse,$key);
        $parent->prependChild($section);
      // if 2 depth merge to next one
      } else if($section->depth === 2) {
        $parent = $this->findNextParent(1,$reverse,$key);
        $parent->prependChild($section);
        // if 1 depth push to colleciton
      } else {
        $nested->push($section);
      }
    }
    return $nested->reverse();
  }

}