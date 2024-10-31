<?php

namespace Library\Command;

use Closure;

class Console
{
  public static $options = [];

  public static function init($argv)
  {
    self::add('📜', 'help', 'Show the help message', fn() => self::help());

    array_shift($argv);

    for ($i = 0; $i < count($argv); $i++) {
      $arg = $argv[$i];
      if (!isset(self::$options[$arg])) break;
      $handler = self::$options[$arg]->handler;
      array_shift($argv);
      $handler($argv);

      exit;
    }

    self::help();
  }

  public static function help()
  {
    $c = time() % 7;
    echo "\033[3{$c}m
@@@@@@@    @@@@@@   @@@@@@@   @@@   @@@@@@   @@@@@@@@@@   
@@@@@@@@  @@@@@@@@  @@@@@@@@  @@@  @@@@@@@   @@@@@@@@@@@  
@@!  @@@  @@!  @@@  @@!  @@@  @@!  !@@       @@! @@! @@!  
!@!  @!@  !@!  @!@  !@!  @!@  !@!  !@!       !@! !@! !@!  
@!@@!@!   @!@!@!@!  @!@@!@!   !!@  !!@@!!    @!! !!@ @!@  
!!@!!!    !!!@!!!!  !!@!!!    !!!   !!@!!!   !@!   ! !@!  
!!:       !!:  !!!  !!:       !!:       !:!  !!:     !!:  
:!:       :!:  !:!  :!:       :!:      !:!   :!:     :!:  
 ::       ::   :::   ::        ::  :::: ::   :::     ::   
 :         :   : :   :        :    :: : :     :      :
\033[0m";

    printf("\nVersion: \033[3{$c}m%s\033[0m <\033[3{$c}m%s\033[0m>", STATE, VERSION);
    printf("\nUsage: \033[3{$c}m%s\033[0m [\033[3{$c}m%s\033[0m] [\033[3{$c}m%s\033[0m]\n\n", 'papcl', 'options', '-f --flag');

    foreach (self::$options as $arg => $command) {
      printf("  \033[3{$c}m%-15s\033[0m%s\n", "{$command->icon} $arg:", $command->description);

      $flags = array_values($command->flags);
      $last = count($flags) - 2;

      foreach (array_chunk($flags, 2) as $i => [$flag]) {
        $column = ($i * 2 >= $last) ? '└-' : '├-';
        printf("  %s    -\033[3{$c}m%-5s\033[0m--\033[3{$c}m%-10s\033[0m%s\n", $column, $flag->short, $flag->long, $flag->description);
      }
    }

    printf("\n%s", "Use the above commands to interact with the system as needed.\n\n");
  }

  public static function add(string $icon, string $name, string $description, Closure $handler)
  {
    $option = new Option($icon, $name, $description, $handler);
    self::$options[$name] = $option;
    return $option;
  }
}
