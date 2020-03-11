<?php

namespace App\Http\Controllers;

use App\Model\Event;
use App\Services\EventService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function create(Request $request){
        try {
            $this->validate(
                $request,
                [
                    'event_name' => 'required',
                    'from'  => 'required',
                    'to'  => 'required',
                    'days' => 'required'
                ]
            );

            $model = new Event();
            $service = new EventService($model);
            $service->create($request);
            return res('Saved',$service->event,200);

        }catch (\Exception $e){
            return res($e->getMessage(),null,400);
        }
    }   

    public function test(){
        return res('This is event controller',null,200);
    }
}
