<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Chat;
use App\Message;
use App\Events\NewMessage;

class MessageController extends Controller
{
    public function getChatMessages($chat_id)
    {
    	$chat = Chat::whereHas('users', function ($query) {
    		$query->where('chat_user.user_id', auth()->id());
    	})->find($chat_id);

    	if (is_null($chat)) abort(401);

    	$messages = Message::with('user')->where('chat_id', $chat_id)
            ->orderBy('id', 'desc')
    		->paginate();

    	return response()->json([ 'messages' => $messages ]);
    }

    public function store(Request $request, $chat_id)
    {
        $request = $request->merge([ 'chat_id' => $chat_id ]);
        $this->validate($request, [
            'chat_id' => 'required|exists:chats,id',
            'body' => 'required'
        ]);

        $chat = Chat::with('users')
            ->whereHas('users', function ($query) {
                $query->where('chat_user.user_id', auth()->id());
            })->find($request->get('chat_id'));

        if (is_null($chat)) abort(401);

        return DB::transaction(function () use ($request, $chat) {
            $message = Message::create([
                'body' => $request->get('body'), 
                'user_id' => auth()->id(), 
                'chat_id' => $chat->id
            ]);
            $message->recievers()->sync($chat->users->pluck('id')->all());
            $message = $message->load('user');
            event(new NewMessage($message, $chat));
            return response()->json([ 'message' => $message ]);
        });
    }
}
