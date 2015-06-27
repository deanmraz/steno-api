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
    $line6 = "list:";
    $line7 = "+ key: value";
    $line8 = "### Key: value";
    $line9 = "description";
    $line10 = "attribute: value";

    $string = "$line0\n$line1\n$line2\n$line3\n$line4\n$line5\n$line6\n$line7\n$line8\n$line9\n$line10\n";

    $lines = $this->callMethod('segmentLines', [$string]);

    //tests lines
    $this->assertEquals($line0,$lines->get(0)->original);
    $this->assertEquals($line1,$lines->get(1)->original);
    $this->assertEquals($line2,$lines->get(2)->original);

    $this->assertEquals($line3,$lines->get(3)->original);

    //test parents
    $this->assertTrue($lines->get(0)->parent);
    $this->assertTrue($lines->get(2)->parent);
    $this->assertTrue($lines->get(8)->parent);

    //not parents
    $this->assertFalse($lines->get(1)->parent);

    //test list items
    $this->assertTrue($lines->get(4)->list_item);
    $this->assertTrue($lines->get(7)->list_item);

    //not list item
    $this->assertFalse($lines->get(0)->list_item);
    $this->assertFalse($lines->get(1)->list_item);

    //test key
    $this->assertEquals('list', $lines->get(3)->key);
    $this->assertEquals('list', $lines->get(6)->key);
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
}