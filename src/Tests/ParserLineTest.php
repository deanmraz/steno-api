<?php

use DMraz\StenoApi\Parsers\Line;

class ParserLineTest extends \PHPUnit_Framework_TestCase
{
  protected function createLine($string, $expectedType)
  {
    $line = new Line($string);
    $this->assertEquals($string, $line->original);
    $this->assertEquals($expectedType, $line->type);
    return $line;
  }

  public function testParent()
  {
    $string = "# Header";
    $line = $this->createLine($string, 'parent');
    $this->assertEquals('Header', $line->text);
  }

  public function testDescription()
  {
    $string = "Description text";
    $line = $this->createLine($string, 'text');
    $this->assertEquals($string, $line->text);
  }

  public function testList()
  {
    $string = "list:";
    $line = $this->createLine($string, 'list');
    $this->assertEquals('list', $line->key);
  }

  public function testListItem()
  {
    $string = "+ item value";
    $line = $this->createLine($string, 'list_item');
    $this->assertEquals('item value', $line->text);
  }

  public function testKeyValue()
  {
    $string = "key: value";
    $line = $this->createLine($string, 'key_value');
    $this->assertEquals('key', $line->key);
    $this->assertEquals('value', $line->value);
  }

  public function testListKeyValue()
  {
    $string = "+ key: value";
    $line = $this->createLine($string, 'list_key_value');
    $this->assertEquals('key', $line->key);
    $this->assertEquals('value', $line->value);
  }
}