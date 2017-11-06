<?php
namespace system;

class Timer {

    private $start;
    private $end;

    public function __construct(){
        $this->start = 0;
        $this->end   = 0;
    }

    public function start(){
        $this->start = microtime(true);
        $this->end   = 0;
    }

    public function end(){
        $this->end   = microtime(true);
    }

    public function getTime($precision = 4) {
        if($this->start >= $this->end){
            return 0;
        }
        return round( ($this->end - $this->start), $precision);
    }
}
