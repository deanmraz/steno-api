<?php

use DMraz\StenoApi\DocumentParser;

class DocumentParserTest extends \PHPUnit_Framework_TestCase
{
  protected $parser;

  protected function setUp()
  {
    $this->parser = new DocumentParser;
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

  public function testSegment()
  {
    $string = "# Header \nDescription \n## Sub Header\n+ list\n";
    $segment = $this->callMethod('segmentDocument', [$string]);
    $this->assertCount(4, $segment);
  }

  public function testFormatNesting()
  {
    $segment = [
      "# Header",
      "Description",
      "  Description",
      "## Sub Header",
      "+ list",
      "  + sub list"
    ];

    $nested = $this->callMethod('formatNesting', [$segment]);
    $this->assertTrue(substr($nested[2],0,2) === '->');
    $this->assertTrue(substr(end($nested),0,2) === '->');
  }

  public function testMatch()
  {
    $formatted = [
      "# Header",
      "Description",
      "->Description",
      "## Sub Header",
      "+ list",
      "->+ sub list"
    ];

    $nested = $this->callMethod('match', [$formatted]);

    //test 1st parent array parent
    $this->assertEquals($formatted[0],$nested[0]['parent']);

    //test 1st parent array 1st child
    $this->assertEquals($formatted[1],$nested[0]['children'][0]);

    //test 1st parent 2nd child
    $this->assertEquals($formatted[2],$nested[0]['children'][1]);

    //test 2nd parent
    $this->assertEquals($formatted[3],$nested[1]['parent']);

    //test 2nd parent 1st child
    $this->assertEquals($formatted[4],$nested[1]['children'][0]);

    //test 2nd parent 2 child
    $this->assertEquals($formatted[5],$nested[1]['children'][1]);
  }

  public function testSubMatch()
  {
    $formatted = [
      [
        'parent' => "# Header",
        'children' => [
          "Description",
          "->Description",
        ]
      ],
     [
        'parent' => "## Sub Header",
        'children' => [
          "+ list",
          "->+ sub list"
        ]
      ]
    ];

    $nested = $this->callMethod('subMatch', [$formatted]);

    $this->assertEquals($formatted[1],$nested[0]['children'][2]);
  }

  public function testGetDocumentVersion()
  {
    $formatted = [
      [
        'parent' => "# Header",
        'children' => [
          "Description",
          "->Description",
        ]
      ],
      [
        'parent' => "# Version: 0.0.0",
        'children' => []
      ]
    ];

    $version = $this->callMethod('getDocumentVersion', [$formatted]);
    $this->assertEquals('0.0.0', $version);
  }
}