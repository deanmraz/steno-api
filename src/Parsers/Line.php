<?php
namespace DMraz\StenoApi\Parsers;

class Line
{
  public $original;
  public $type;

  protected $parent; //can be parent?
  protected $list_item;

  public $parent_types = ['###','##','#'];
  public $list_item_types = ['+'];

  public $leads;  // the lead characters
  public $key;
  public $value;
  public $text;

  public function __construct($line)
  {
    $this->original = $line;
    $this->parseParent();
    $this->parseListItem();
    $this->parseKey();
    $this->parseValue();
    $this->parseText();
    $this->parseType();
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
    $original = trim($this->original);
    // only if it does not have key can it be plain text
    if(!$this->key) {
      if($leads = $this->leads) {
        $this->text = trim(str_replace($leads,'',$original));
      } else if(!empty($original)){
        $this->text = $original;
      }
    } else {
      $this->text = false;
    }
  }

  protected function parseType()
  {
    if($this->parent) $this->type = 'parent';
    else if($this->list_item && $this->key && $this->value) $this->type = 'list_key_value';
    else if($this->list_item && !$this->key && !$this->value) $this->type = 'list_item';
    else if($this->key && !$this->value) $this->type = 'list';
    else if($this->key && $this->value) $this->type = 'key_value';
    else if($this->text) $this->type = 'text';
    else $this->type = 'none';
  }
}