<?php

use DMraz\StenoApi\StenoParsers\Version_0_0_0 as Parser;

class StenoVersion000Test extends \PHPUnit_Framework_TestCase
{
  protected $parser;

  protected function setUp()
  {
    $this->parser = new Parser;
  }

  protected function callMethod($name, array $args) {
    $class = new \ReflectionClass($this->parser);
    $method = $class->getMethod($name);
    $method->setAccessible(true);
    return $method->invokeArgs($this->parser, $args);
  }

  public function testGetDocumentVersion()
  {
    $formatted = [
      [
        'parent' => "# Version: 0.0.0",
        'children' => []
      ],
      [
        'parent' => "# API: ApiName",
        'children' => [
        ]
      ],
      [
        'parent' => "# Resource: ResourceName",
        'children' => [
          '+ id, number, description'
        ]
      ],
      [
        'parent' => "# Operation: GET /path/to/api",
        'children' => [
        ]
      ]
    ];

    $this->callMethod('parse', [$formatted]);

    $this->assertEquals('ApiName', $this->parser->getApi()['name']);
    $this->assertEquals('ResourceName', $this->parser->getResource()['name']);
    $this->assertEquals('GET', $this->parser->getOperations()[0]['method']);
    $this->assertEquals('/path/to/api', $this->parser->getOperations()[0]
['uri']);

  }

  public function testSetOperation()
  {
    $section =  [
      'parent' => "# Operation: GET /path/to/api",
      'children' => [
        [
          'parent' => "## Filters",
          'children' => [
            '+ Filter 1'
          ]
        ],
        [
          'parent' => "## Parameters",
          'children' => [
            '+ id, number, unique id'
          ]
        ],
        [
          'parent' => "## Validations",
          'children' => [
            '+ id, required|number, unique id'
          ]
        ],
        [
          'parent' => "## Example",
          'children' => [],
        ],
        'Description for operation'
      ]
    ];

    $this->callMethod('setOperation', [$section]);

    $operations = $this->parser->getOperations();

    $this->assertEquals('/path/to/api', $operations[0]['uri']);
    $this->assertEquals('GET', $operations[0]['method']);
    $this->assertEquals('Description for operation', $operations[0]['description']);
    $this->assertEquals('Filter 1', $operations[0]['filters'][0]);
    $this->assertEquals('required|number', $operations[0]['validations'][0]['type']);
    $this->assertEquals('required|number', $operations[0]['validations'][0]['type']);

  }

}