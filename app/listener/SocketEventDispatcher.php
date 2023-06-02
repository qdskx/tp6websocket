<?php
declare (strict_types = 1);

namespace app\listener;

use think\facade\Event;
use think\helper\Str;

class SocketEventDispatcher
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($event)
    {
//        var_dump('SocketEventDispatcher', $event);
//        array(2) {
//            ["type"]=>
//              string(4) "join"
//                    ["data"]=>
//              array(1) {
//                        [0]=>
//                array(1) {
//                            ["room"]=>
//                  string(1) "1"
//                }
//              }
//            }
        ['type' => $type, 'data' => $data]  = $event;
//        Event::trigger('swoole.websocket.Event.' . Str::studly($type), $data[0]);
        Event::trigger(Str::studly($type), $data[0]);
    }
}
