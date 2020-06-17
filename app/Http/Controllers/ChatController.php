<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Chat;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Events\NewUserChat;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $chats = Chat::with('users')
        	->select(DB::raw('ANY_VALUE(chats.id) as id, ANY_VALUE(chats.channel_identifier) as channel_identifier, ANY_VALUE(chats.created_at) as created_at, ANY_VALUE(chats.updated_at) as updated_at, count(message_user.id) as unseen_message_count, ANY_VALUE(messages.created_at) as message_created_at'))
            ->leftJoin('messages', 'messages.chat_id', 'chats.id')
            ->leftJoin('message_user', 'message_user.message_id', 'messages.id')
            ->where(function ($query) {
    	        $query->whereHas('users', function ($innerQuery) {
    	        	$innerQuery->where('chat_user.user_id', auth()->id());
    	        })
    	        ->orWhereHas('messages.recievers', function ($innerQuery) {
    	        	$innerQuery->where('message_user.user_id', auth()->id());
    	        });
            })
	        ->orderBy('unseen_message_count', 'desc')
	        ->orderBy('messages.id', 'desc')
            ->groupBy('chats.id')
	        ->paginate();

        collect($chats->items())->each(function($chat) {
            $chat->load([ 'messages' => function ($query) {
                $query->orderBy('id', 'desc')->take(1);
            }, 'messages.recievers', 'messages.user' ]);
        });

	    return response()->json([ 'chats' => $chats ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'sym_key' => 'required',
            'users.*.sym_key' => 'required',
            'users.*.id' => 'required|exists:users,id|not_in:' . auth()->id(),
        ]);

        $channel_identifier = str_random(16);
        $chat_with_id = Chat::where('channel_identifier', $channel_identifier)->first();
        while (!is_null($chat_with_id)) {
            $channel_identifier = str_random(16);
            $chat_with_id = Chat::where('channel_identifier', $channel_identifier)->first();
        }

        $chat = Chat::create([ 'channel_identifier' => $channel_identifier ]);
        $data = [ auth()->id() => [ 'key' => $request->get('sym_key'), 'nickname' => auth()->user()->name ] ];
        foreach ($request->get('users') as $user) {
            $userObj = User::find(array_get($user, 'id'));
            $data[$userObj->id] = [ 'key' => array_get($user, 'sym_key'), 'nickname' => $userObj->name ];
        }
        $chat->users()->sync($data);
        foreach ($data as $user_id => $user_data) {
            if ($user_id != auth()->id())
                event(new NewUserChat($user_id, $chat->id));
        }

        $chat->load(['messages.recievers', 'messages.user', 'users']);
        return response()->json([ 'chat' => $chat ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $chat = Chat::with([ 'messages' => function ($query) {
                $query->orderBy('id', 'desc')->take(1);
            }, 'messages.recievers', 'messages.user', 'users' ])
            ->whereHas('users', function ($query) {
                $query->where('chat_user.user_id', auth()->id());
            })->find($id);

        if (is_null($chat)) abort(404);

        return response()->json([ 'chat' => $chat ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getConversation(Request $request)
    {
        $this->validate($request, [
            'user_ids.*' => 'min:1|exists:users,id'
        ]);

        $user_ids = $request->get('user_ids');
        if (!is_array($user_ids)) $user_ids = [ $user_ids ];
        $user_ids = array_unique($user_ids);
        array_pull($user_ids, auth()->id());

        $coversation_with_users = Chat::with([ 'messages' => function ($query) {
                $query->orderBy('id', 'desc');
            }, 'messages.recievers', 'messages.user' ])
            ->whereDoesntHave('users', function ($query) use ($user_ids) {
                array_push($user_ids, auth()->id());
                $query->whereNotIn('chat_user.user_id', $user_ids);
            })
            ->first();

        return response()->json([ 'chat' => $coversation_with_users ]);
    }
}
