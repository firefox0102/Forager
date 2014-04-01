<?php
class AsyncOperation extends Thread {
  public function __construct($arg){
    $this->arg = $arg;
  }

  public function run(){
    if($this->arg){
      echo "Hello " .$this->arg;
    }
  }
}
$thread = new AsyncOperation("World");
if($thread->start())
  $thread->join();
?>