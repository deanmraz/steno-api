<?php
namespace DMraz\StenoApi\Parsers;

class Line
{
  public $number;
  public $after;

  public $original;

  public $parent; //can be parent?
  public $list_item;

  public $parent_types = ['###','##','#'];
  public $list_item_types = ['+'];

  public $leads;  // the lead characters

  public $key;
  public $value;
  public $text;

  public function __construct($line, $number)
  {
    $this->original = $line;
    $this->number = $number;
    $this->after = $number === 0 ? null : $number - 1;
    $this->parseParent();
    $this->parseListItem();
    $this->parseKey();
    $this->parseValue();
    $this->parseText();
  }

  protected function parseParent()
  {
    $segment = explode(' ',$this->original);
    if(array_search($segment[0],$this->parent_types)!==false) {
      $this->leads = $segment[0];
      $this->parent = true;
    } else {
      $this->parent = false;
    }
  }


  protected function parseListItem()
  {
    $segment = explode(' ',$this->original);
    if(array_search($segment[0],$this->list_item_types)!==false) {
      $this->leads = $segment[0];
      $this->list_item = true;
    } else {
      $this->list_item = false;
    }
  }

  protected function parseKey()
  {
    $segment = explode(' ',$this->original);
    foreach($segment as $item)
    {
      if(substr($item, -1)===':') {
        $this->key = substr($item, 0, -1);
        break;
      }
    }
  }

  protected function parseValue()
  {
    $segment = explode(':',$this->original);
    if(isset($segment[1])) {
      $value = trim($segment[1]);
      if(!empty($value)) {
        $this->value = $value;
      }
    }
  }

  protected function parseText()
  {
    // only if it does not have key can it be plain text
    if(!$this->key) {
      if($leads = $this->leads) {
        $this->text = trim(str_replace($leads,'',$this->original));
      } else {
        $this->text = $this->original;
      }
    } else {
      $this->text = false;
    }
  }
}