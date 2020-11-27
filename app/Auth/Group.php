<?php

namespace App\Auth;

class Group {

  /**
   * @param string
  */
  public $name;

  /**
   * @param array
  */
  public $attributes;

  public function __construct(array $params = [])
  {
    $this->attributes = [];
    if(!empty($params))
      foreach($params as $k => $v) $this->{$k} = $v;
  }

}