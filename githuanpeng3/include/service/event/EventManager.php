<?php
namespace service\event;
use service\event\EventAbstract;
use service\user\UserEvent;
use service\live\LiveEvent;
use service\room\RoomEvent;
use service\anchor\AnchorEvent;

class EventManager extends EventAbstract
{

    private $events = [];


    private function _initEvents()
    {

        $this->events['userEvent'] = new UserEvent;
        $this->events['liveEvent'] = new LiveEvent;
        $this->events['roomEvent'] = new RoomEvent;
        $this->events['anchorEvent'] = new AnchorEvent;
    }

    public function trigger($action,$param)
    {
        $this->_initEvents();
        foreach ($this->events as $name => $event)
        {
            $event->trigger($action,$param);
        }

        return true;
    }
}