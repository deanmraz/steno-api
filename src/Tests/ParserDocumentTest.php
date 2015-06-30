<?php

use DMraz\StenoApi\Parsers\Document;

class ParserDocumentTest extends \PHPUnit_Framework_TestCase
{
  protected $parser;
  protected $string;

  protected function setUp()
  {
    $this->parser = new Document;

    $line0 = "# Header";
    $line1 = "Description";
    $line2 = "## Sub Header";
    $line3 = "list:";
    $line4 = "+ item value";
    $line5 = "attribute: attribute_value";
    $line6 = "lists:";
    $line7 = "+ key: value";
    $line8 = "### Key: value";
    $line9 = "description";
    $line10 = "attribute: value";
    $line11 = "### Double 3 Depth";
    $line12 = "## Double 2 Depth";
    $line13 = "# Another 1st level parent";
    $line14 = "Description";
    $line15 = "+ key: ";
    $line16 = "{";
    $line17 = '"json":true';
    $line18 = "}";

    $this->string = "$line0\n$line1\n$line2\n$line3\n$line4\n$line5\n$line6\n$line7\n$line8\n$line9\n$line10\n$line11\n$line12\n$line13\n$line14\n$line15\n$line16\n$line17\n$line18\n";

  }

  protected function callMethod($name, array $args) {
    $class = new \ReflectionClass($this->parser);
    $method = $class->getMethod($name);
    $method->setAccessible(true);
    return $method->invokeArgs($this->parser, $args);
  }

  public function testCleanup()
  {
    $string = "\n\r \n \r \n\r \r\n";
    $clean = $this->callMethod('cleanup', [$string]);
    $this->assertFalse(strpos($clean, "\r"));
  }

  public function testLines()
  {
    $string = $this->string;

    $lines = $this->callMethod('segmentLines', [$string]);

    //test types
    $this->assertEquals('parent', $lines->get(0)->type);
    $this->assertEquals('text', $lines->get(1)->type);
    $this->assertEquals('parent', $lines->get(2)->type);
    $this->assertEquals('list', $lines->get(3)->type);
    $this->assertEquals('list_item', $lines->get(4)->type);
    $this->assertEquals('key_value', $lines->get(5)->type);
    $this->assertEquals('list', $lines->get(6)->type);
    $this->assertEquals('list_key_value', $lines->get(7)->type);
    $this->assertEquals('parent', $lines->get(8)->type);
    $this->assertEquals('text', $lines->get(9)->type);
    $this->assertEquals('key_value', $lines->get(10)->type);

    //test key
    $this->assertEquals('list', $lines->get(3)->key);
    $this->assertEquals('lists', $lines->get(6)->key);
    $this->assertEquals('attribute', $lines->get(5)->key);
    $this->assertEquals('key', $lines->get(7)->key);
    $this->assertEquals('Key', $lines->get(8)->key);
    $this->assertEquals('attribute', $lines->get(10)->key);

    //test value
    $this->assertEquals('attribute_value', $lines->get(5)->value);
    $this->assertEquals('value', $lines->get(7)->value);
    $this->assertEquals('value', $lines->get(8)->value);
    $this->assertEquals('value', $lines->get(10)->value);

    //test text
    $this->assertEquals('Header', $lines->get(0)->text);
    $this->assertEquals('Description', $lines->get(1)->text);
    $this->assertEquals('Sub Header', $lines->get(2)->text);
    $this->assertEquals('item value', $lines->get(4)->text);

    //not text
    $this->assertFalse($lines->get(6)->text);
    $this->assertFalse($lines->get(8)->text);
    $this->assertFalse($lines->get(10)->text);

  }

  public function testSections()
  {
    $string = $this->string;

    $lines = $this->callMethod('segmentLines', [$string]);

    $sections = $this->callMethod('segmentSections', [$lines]);

    //section 1
    $this->assertEquals('Header', $sections->get(0)->title);
    $this->assertEquals('Description', $sections->get(0)->getDescription());

    //section 2
    $this->assertEquals('Sub Header', $sections->get(1)->title);
    $this->assertNull($sections->get(1)->getDescription());
    $list = $sections->get(1)->get('list');
    $this->assertTrue(is_array($list));
    $this->assertEquals('item value',$list[0]);
    $this->assertEquals('attribute_value', $sections->get(1)->get('attribute'));
    $this->assertEquals('value',$sections->get(1)->get('lists.key'));

    //section 3
    $this->assertEquals('Key', $sections->get(2)->key);
    $this->assertEquals('value', $sections->get(2)->value);
    $this->assertEquals('description', $sections->get(2)->getDescription());
    $this->assertEquals('value', $sections->get(2)->get('attribute'));

    //ensure text gets compiled properly
    $this->assertEquals('{"json":true}', $sections->last()->get('key'));
    $this->assertEquals('Description', $sections->last()->getDescription());

  }

  public function testNested()
  {
    $string = $this->string;

    $lines = $this->callMethod('segmentLines', [$string]);

    $sections = $this->callMethod('segmentSections', [$lines]);

    $nested = $this->callMethod('nestSections', [$sections]);

    //test nested
    $this->assertEquals('Header', $nested->get(0)->title);
    $this->assertEquals('Another 1st level parent', $nested->get(1)->title);

    $this->assertEquals('Sub Header', $nested->get(0)->children[0]->title);
    $this->assertEquals('Double 2 Depth', $nested->get(0)->children[1]->title);

    $this->assertEquals('Key', $nested->get(0)->children[0]->children[0]->key);
    $this->assertEquals('Double 3 Depth', $nested->get(0)->children[0]->children[1]->title);

  }
}