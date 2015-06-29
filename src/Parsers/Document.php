<?php
namespace DMraz\StenoApi\Parsers;

use Illuminate\Support\Collection;

class Document
{
  protected $lines;
  protected $sections;

  public function parse($document)
  {
    $document = $this->cleanup($document);

    # get line collection
    $this->lines = $lines = $this->segmentLines($document);

    # section the lines
    $this->sections = $sections = $this->segmentSections($lines);
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

  protected function isAssociativeArray($array)
  {
    return array_keys($array) !== range(0, count($array) - 1);
  }

  protected function segmentSections(Collection $lineCollection)
  {
    $sections = new Collection;
    $lines = $lineCollection->all();
    $lastSection = null;
    $lastListAttribute = null;
    foreach($lines as $key => $line)
    {
      $method = "segment".studly_case($line->type)."Line";
      $result = $this->$method($line, $lastSection, $lastListAttribute);

      //set section
      if(is_object($result)) {
        $sections->push($result);
        $lastSection = $sections->last();
      // set last list attribute
      } else if(!empty($result)) {
        $lastListAttribute = $result;
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

  /**
   * @param $line
   * @param $section Section
   */
  protected function segmentTextLine($line, $section)
  {
    $section->setDescription($line->text);
  }

  /**
   * @param $line
   * @param $section Section
   */
  protected function segmentListLine($line, $section)
  {
    $section->addAttributeList($line->key);
    return $line->key;
  }

  /**
   * @param $line
   * @param $section Section
   */
  protected function segmentListItemLine($line, $section, $attribute)
  {
    $section->addAttributeListItem($attribute, $line->text);
  }

  /**
   * @param $line
   * @param $section Section
   */
  protected function segmentListKeyValueLine($line, $section, $attribute)
  {
    $section->addAttributeListKeyValue($line->key, $line->value, $attribute);
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
  protected function segmentNoneLine($line, $section)
  {

  }

}