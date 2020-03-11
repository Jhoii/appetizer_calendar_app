<?php


namespace App\Services;


use App\Model\Event;

class EventService
{
    public $event;


    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * @throws \Exception
     */
    public function create($request)
    {
        $event = $this->event::create($request->all());
        if(!$event){
            throw new \Exception('Failed to save event data');
        }
        $this->event = $event;
    }

}
