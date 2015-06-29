<?php

use DMraz\StenoApi\Parsers\Document;

class ParserDocumentTest extends \PHPUnit_Framework_TestCase
{
  protected $parser;

  protected function setUp()
  {
    $this->parser = new Document;
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
    $line0 = "# Header";
    $line1 = "Description";
    $line2 = "## Sub Header";
    $line3 = "list:";
    $line4 = "+ item value";
    $line5 = "key: value";
    $line6 = "lists:";
    $line7 = "+ key: value";
    $line8 = "### Key: value";
    $line9 = "description";
    $line10 = "attribute: value";

    $string = "$line0\n$line1\n$line2\n$line3\n$line4\n$line5\n$line6\n$line7\n$line8\n$line9\n$line10\n";

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

    //tests lines
    $this->assertEquals($line0,$lines->get(0)->original);
    $this->assertEquals($line1,$lines->get(1)->original);
    $this->assertEquals($line2,$lines->get(2)->original);
    $this->assertEquals($line3,$lines->get(3)->original);

    //test key
    $this->assertEquals('list', $lines->get(3)->key);
    $this->assertEquals('lists', $lines->get(6)->key);
    $this->assertEquals('key', $lines->get(5)->key);
    $this->assertEquals('key', $lines->get(7)->key);
    $this->assertEquals('Key', $lines->get(8)->key);
    $this->assertEquals('attribute', $lines->get(10)->key);

    //test value
    $this->assertEquals('value', $lines->get(5)->value);
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

    $string = "$line0\n$line1\n$line2\n$line3\n$line4\n$line5\n$line6\n$line7\n$line8\n$line9\n$line10\n";

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
  }
}