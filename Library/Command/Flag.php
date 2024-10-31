<?php

namespace Library\Command;

use Closure;

class Flag
{
  public string $short;
  public string $long;
  public string $description;
  public Closure $handler;
  public int $argc;

  public function __construct(string $short, string $long, string $description, int $argc, Closure $handler)
  {
    $this->short = $short;
    $this->long = $long;
    $this->description = $description;
    $this->argc = $argc;
    $this->handler = $handler;
  }
}
