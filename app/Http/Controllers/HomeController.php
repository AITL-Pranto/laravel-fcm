<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Facades\FCM;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $chats = Chat::all();
        return view('home')->with('chats', $chats);
    }

    public function createChat(Request $request)
    {
        $input = $request->all();

        $message = $input['message'];

        $chat = new Chat([
            'sender_id' => auth()->user()->id,
            'sender_name' => auth()->user()->name,
            'message' => $message,
        ]);

        $this->broadcastMessage(auth()->user()->name, $message);

        if ($chat->save()) {
            $data_generate = '';
            $data_generate .= '<div class="chat-container">';

            //Get all conversation
            $chats = Chat::all();
            //Get all conversation

            if (isset($chats[0])) {
                foreach ($chats as $chat) {
                    if ($chat->sender_id == Auth::user()->id) {
                        $data_generate .= '<p class="chat chat-left"><b>' . $chat->sender_name . '</b><br> ' . $chat->message . '</p>';
                    } else {
                        $data_generate .= '<p class="chat chat-right"><b>' . $chat->sender_name . '</b><br> ' . $chat->message . '</p>';
                    }
                }
            } else {
                $data_generate .= '<p class="text-center text-danger">No Conversation Found!!</p>';
            }
            $data_generate .= '</div>';
        }
        return response()->json(array('success' => true, 'data_generate' => $data_generate));
    }

    private function broadcastMessage($senderName, $message)
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder('New Message From ' . $senderName);
        $notificationBuilder->setBody($message)->setSound('default')->setClickAction('http://127.0.0.1:8000/home');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'sender_name' => $senderName,
            'message' => $message
        ]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $tokens = User::all()->pluck('fcm_token')->toArray();

        $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);

        $downstreamResponse->numberSuccess();
    }

    public function loadConversation()
    {
        if (isset($_GET['conversation']) == 'new_conversation') {
            $data_generate = '';
            $data_generate .= '<div class="chat-container">';

            //Get all conversation
            $chats = Chat::all();
            //Get all conversation

            if (isset($chats[0])) {
                foreach ($chats as $chat) {
                    if ($chat->sender_id == Auth::user()->id) {
                        $data_generate .= '<p class="chat chat-left"><b>' . $chat->sender_name . '</b><br> ' . $chat->message . '</p>';
                    } else {
                        $data_generate .= '<p class="chat chat-right"><b>' . $chat->sender_name . '</b><br> ' . $chat->message . '</p>';
                    }
                }
            } else {
                $data_generate .= '<p class="text-center text-danger">No Conversation Found!!</p>';
            }
            $data_generate .= '</div>';
            return response()->json(array('success' => true, 'data_generate' => $data_generate));
        }
    }
}
