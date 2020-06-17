<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
	/**
	 * Fields that can be mass assigned.
	 *
	 * @var array
	 */
	protected $fillable = [ 'channel_identifier' ];
	
    public function users()
    {
    	return $this->belongsToMany(User::class)
    		->using(ChatUser::class)
            ->withPivot([ 'key', 'nickname' ])
            ->withTimeStamps();
    }

    /**
     * Chat has many Messages.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
    	// hasMany(RelatedModel, foreignKeyOnRelatedModel = chat_id, localKey = id)
    	return $this->hasMany(Message::class);
    }
}
