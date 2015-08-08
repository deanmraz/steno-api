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

  public function testListSpace()
  {
    $string = "list space:";
    $line = $this->createLine($string, 'list');
    $this->assertEquals('list space', $line->key);
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

  public function testNoneString()
  {
    $string = "";
    $line = $this->createLine($string, 'none');
    $this->assertNull($line->key);
    $this->assertNull($line->value);
    $this->assertNull($line->text);
  }

  public function testNoneReturn()
  {
    $string = "\n\r";
    $line = $this->createLine($string, 'none');
    $this->assertNull($line->key);
    $this->assertNull($line->value);
    $this->assertNull($line->text);
  }
}