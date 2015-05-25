<?php namespace DMraz\StenoApi\StenoParsers;

class Version_0_0_0
{
  protected $api;
  protected $resource;
  protected $operations;

  public function parse($document)
  {
    foreach($document as $section)
    {
      $line = isset($section['parent']) ? $section['parent'] : null;
      if($line)
      {
        if($this->isApi($line)) $this->setApi($section);
        else if($this->isResource($line)) $this->setResource($section);
        else if($this->isOperation($line)) $this->setOperation($section);
      }
    }
  }

  protected function isApi($line)
  {
    $clean = strtolower(trim(trim($line,'#')));
    return substr($clean, 0, 3) === 'api';
  }

  protected function setApi($section)
  {
    $this->api = [
      'name' => ucfirst(trim(substr($section['parent'], 6))),
      'description' => !empty($section['children'][0]) ? $section['children'][0] : null
    ];
  }

  protected function isResource($line)
  {
    $clean = strtolower(trim(trim($line,'#')));
    return substr($clean, 0, 8) === 'resource';
  }

  protected function setResource($section)
  {
    $attributes = [];
    foreach($section['children'] as $string)
    {
      $attributes[] = $this->segmentAttributeType($string);
    }
    $this->resource = [
      'name' => ucfirst(trim(substr($section['parent'], 11))),
      'attributes' => $attributes,
    ];
  }

  protected function isOperation($line)
  {
    $clean = strtolower(trim(trim($line,'#')));
    return substr($clean, 0, 9) === 'operation';
  }

  protected function isSection($name, $line, $trim = '#')
  {
    $clean = strtolower(trim(trim($line,$trim)));
    return substr($clean, 0, strlen($name)) === $name;
  }

  protected function setOperation($section)
  {
    //uri & method
    $line = trim(substr($section['parent'], 12));
    $segment = explode(' ',$line);

    $examples = [];

    foreach($section['children'] as $child)
    {
      //description
      if(is_string($child)) $description = $child;
      //filters
      else if($this->isFilters($child['parent'])) $filters = $this->setFilters($child);
      //parameters
      else if($this->isParameters($child['parent'])) $parameters = $this->setParameters($child);
      //validations
      else if($this->isValidations($child['parent'])) $validations = $this->setValidations($child);
      //examples
      else if($this->isExample($child['parent'])) $examples[] = $this->setExample($child);
    }

    $this->operations[] = [
      'uri' => isset($segment[1]) ? $segment[1] : null,
      'method' => isset($segment[0]) ? strtoupper($segment[0]) : null,
      'description' => isset($description) ? $description : null,
      'filters' => isset($filters) ? $filters : null,
      'parameters' => isset($parameters) ? $parameters : null,
      'validations' => isset($validations) ? $validations : null,
      'examples' => isset($examples) ? $examples : null,
    ];
  }

  protected function isFilters($line)
  {
    return $this->isSection('filters',$line,'##');
  }

  protected function setFilters($section)
  {
    $filters = [];
    foreach($section['children'] as $filter)
    {
      $filters[] = trim($filter,'+ ');
    }
    return $filters;
  }

  protected function isParameters($line)
  {
    return $this->isSection('parameters',$line,'##');
  }

  protected function setParameters($section)
  {
    $parameters = [];
    foreach($section['children'] as $parameter)
    {
      $parameters[] = $this->segmentAttributeType($parameter);
    }
    return $parameters;
  }

  protected function segmentAttributeType($string)
  {
    $segment = explode(',', $string);
    return [
      'name' => isset($segment[0]) ? trim($segment[0],'+ ') : null,
      'type' => isset($segment[1]) ? trim($segment[1]) : null,
      'description' => isset($segment[2]) ? trim($segment[2]) : null,
    ];
  }

  protected function isValidations($line)
  {
    return $this->isSection('validations',$line,'##');
  }

  protected function setValidations($section)
  {
    $validations = [];
    foreach($section['children'] as $validation)
    {
      $validations[] = $this->segmentAttributeType($validation);
    }
    return $validations;
  }

  protected function isExample($line)
  {
    return $this->isSection('example',$line,'##');
  }

  protected function setExample($section)
  {
    $segment = $this->segmentExample($section['children']);

    return $this->createExample($segment);
  }

  protected function segmentExample($children)
  {
    $segment = [];
    $last_key = null;
    $last_line_key = null;
    foreach($children as $line)
    {
      $substr = trim(substr($line,0,2));
      if($substr === '+')
      {
        if(is_null($last_key)) $last_key = 0;
        else $last_key++;
        $segment[] = [
          'parent' => $line,
          'children' => []
        ];

        $last_line_key = null;
      }
      else
      {
        $substr = trim(substr(trim($line),0,2));
        if($substr === '+')
        {
          if(is_null($last_line_key)) $last_line_key = 0;
          else $last_line_key++;
        }
        if(empty($segment[$last_key]['children'][$last_line_key])) $segment[$last_key]['children'][$last_line_key] = '';
//        echo "$last_line_key $line \n";
        $segment[$last_key]['children'][$last_line_key] .= trim($line);
      }
    }

    return $segment;
  }

  protected function createExample($segment)
  {
    $example = [];
    foreach($segment as $sub_section)
    {
      $key = trim($sub_section['parent'],'+ ');
      $properties = [];
      foreach ($sub_section['children'] as $child)
      {
        $line = trim($child,'+ ');

        if(($position = strpos($line, 'Body:')) !== false)
        {
          $properties['Body'] = substr($line,strlen('Body:'));
        }
        else if(($position = strpos($line, 'Payload:')) !== false)
        {
          $properties['Payload'] = substr($line,strlen('Payload:'));
        }
        else
        {
          $split = explode(':',$line);
          $property = str_replace(['-',' '],'',$split[0]);
          $properties[$property] = trim($split[1]);
        }
      }
      $example[$key] = $properties;
    }

    return $example;
  }

  /**
   * @return mixed
   */
  public function getOperations()
  {
    return $this->operations;
  }

  /**
   * @return mixed
   */
  public function getResource()
  {
    return $this->resource;
  }

  /**
   * @return mixed
   */
  public function getApi()
  {
    return $this->api;
  }

}
