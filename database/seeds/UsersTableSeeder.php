<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Helpers\SeedHelper;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	\DB::table('users')->truncate();

        $key_data = [
    		'public_key' => 'lCPETykvzp5J3JoFCF9oSMupBlV5oVIotke+Wa/D84DUz9HnUPJhT4iqNFWC1PzAYWmS4ph1TZ3dltS6U0Or7g==',
            'data' => '{"iv":"VwYRmD1xRJVF3BwYwsUABA==","v":1,"iter":1000,"ks":128,"ts":64,"mode":"ccm","adata":"","cipher":"aes","salt":"OeqlSdoZLiI=","ct":"z0up6BGaCiV/YXfI14taIkohm9VVnaeQgqG+zvXzT4LnUu3NpQPucgKDAYk2KMLJR0SiB+/LQpxOyE/6hH8LQbcEd8uuXqZP3XoPDEaeAqJfiwbIpsL7mtdMrhmYgs07g66WTLBUrw/yGkOQ4LdL39P+Oin7s3C7ZCrP/XgR3wIVlT3EWWCRvKH/0EU9ef8+zmW4lhJFa2LoOCDU3IIZME26jz0g5gKuIVFRvu4/21YGkEl7fh9ZWs0A6bcPuiK1j53i8/s4sMcIYzr5WmEv"}'
        ];

        User::create([
            'name' => 'Ravisha Heshan',
            'email' => 'ravishaheshan@gmail.com',
            'password' => '6481f8e1a060d56eeb7c10ac7809d316800dce013713c412e1d22076505b11a8'
        ] + $key_data);

        User::create([
            'name' => 'Madushan Lamahewa',
            'email' => 'madushanlamahewa@gmail.com',
            'password' => '6481f8e1a060d56eeb7c10ac7809d316800dce013713c412e1d22076505b11a8'
        ] + $key_data);


        for ($i=1; $i < 5; $i++) { 
        	User::create([
    	        	'name' => 'User' . $i,
    	        	'email' => 'msngruser' . $i . '@example.com',
    	        	'password' => '6481f8e1a060d56eeb7c10ac7809d316800dce013713c412e1d22076505b11a8'
                ] + $key_data);
        }
    }
}
