<?php

namespace Library\Command;

use Closure;
use Library\Command\Flag;

class Option
{
  public string $description;
  public array $flags = [];
  public Closure $handler;
  public string $name;
  public string $icon;

  public function flag_add(Flag $flag)
  {
    $this->flags["-{$flag->short}"] = $flag;
    $this->flags["--{$flag->long}"] = $flag;
  }

  public function __construct(string $icon, string $name, string $description, Closure $handler)
  {
    $this->description = $description;
    $this->handler = $handler;
    $this->name = $name;
    $this->icon = $icon;
  }
}
