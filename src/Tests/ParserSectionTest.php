<?php

use DMraz\StenoApi\Parsers\Section;

class ParserSectionTest extends \PHPUnit_Framework_TestCase
{
  protected $section;

  public function setUp()
  {
    $this->section = new Section;
  }

  public function testAddAttributeKeyValue()
  {
    $this->section->addAttributeKeyValue('key', 'value');

    $this->assertEquals('value', $this->section->get('key'));

  }

  public function testAddingListSequential()
  {
    $this->section->addAttributeList('list');
    $this->section->addAttributeListItem('list','item1');
    $this->section->addAttributeListItem('list','item2');
    $this->section->addAttributeListItem('list','item3');

    $list = $this->section->get('list');
    $this->assertEquals('item1', $list[0]);
    $this->assertEquals('item2', $list[1]);
    $this->assertEquals('item3', $list[2]);
  }

  public function testAddingListKeyValue()
  {
    $this->section->addAttributeList('list');
    $this->section->addAttributeListKeyValue('key1','value1','list');
    $this->section->addAttributeListKeyValue('key2','value2','list');
    $this->section->addAttributeListKeyValue('key3','value3','list');

    $this->assertEquals('value1', $this->section->get('list.key1'));
    $this->assertEquals('value2', $this->section->get('list.key2'));
    $this->assertEquals('value3', $this->section->get('list.key3'));
  }

  /**
   * @expectedException Exception
   */
  public function testAddAttributeKeyValueHasBeenSet()
  {
    $this->section->addAttributeKeyValue('key', 'value');
    $this->section->addAttributeKeyValue('key', 'value');
  }

  /**
   * @expectedException Exception
   */
  public function testChangingAttributeListSequentialToKeyValue()
  {
    $this->section->addAttributeList('list');
    $this->section->addAttributeListItem('list','item');
    $this->section->addAttributeListKeyValue('key','value','list');
  }

  /**
   * @expectedException Exception
   */
  public function testChangingAttributeListKeyValueToSequential()
  {
    $this->section->addAttributeList('list');
    $this->section->addAttributeListKeyValue('key','value','list');
    $this->section->addAttributeListItem('list','item');
  }
}