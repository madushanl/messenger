<?php

use Illuminate\Database\Seeder;
use Faker\Factory;
use App\Chat;
use App\User;
use App\Message;

class ChatsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('chat_user')->truncate();
        \DB::table('chats')->truncate();

        $faker = Factory::create();

        $users = User::whereIn('email', [ 'ravishaheshan@gmail.com', 'madushanlamahewa@gmail.com' ])->get();
        foreach ($users as $user) {
           for ($i=0; $i < 4; $i++) {
                $user_chats = $user->load('chats')->chats;
                $other_user = User::whereDoesntHave('chats', function ($query) use ($user_chats) {
                    $query->whereIn('chat_id', $user_chats->pluck('id')->all());
                })->whereNotIn('id', $users->pluck('id')->all())->get()->random();

                $key = '{"iv":"gJm+IR6MYgors7t6tQd4xA==","v":1,"iter":1000,"ks":128,"ts":64,"mode":"ccm","adata":"","cipher":"aes","kemtag":"ZhoPsxeBCm2qmvZ7dCOHPv4MLm1BKPfIu0KV31ARhuHG2zJo6cHxDvEyqq28b5fQg8aaEMLQKE1xMXUEOUkpfw==","ct":"ZuBXH/s/azZpd004fjHgttUWeN0Y54ugRwt+RhJDI+zEKli6Celd0MMLNf9xKnmInYhmHA=="}';
                $chat = Chat::create( [ 'channel_identifier' => str_random(16) ] );
                $chat->users()->sync([
                        $user->id => [
                            'nickname' => $user->name,
                            'key' => $key
                        ],
                        $other_user->id => [
                            'nickname' => $other_user->name,
                            'key' => $key
                        ]
                    ]);

                // for ($j=0; $j < 50; $j++) {
                //     $chat_users = collect([ $user, $other_user ]);
                //     $owner = $chat_users->random();
                //     $recievers = $chat_users->whereNotIn('id',[ $owner->id ]); 
                //     $message = Message::create([ 
                //         'body' => $faker->paragraph(rand(1, 4)), 
                //         'user_id' => $owner->id, 
                //         'chat_id' => $chat->id
                //     ]);

                //     $message->recievers()->sync($recievers->pluck('id')->all());
                // }
           }
        }
    }
}
