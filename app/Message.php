<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = [ 'body', 'user_id', 'chat_id' ];

    /**
     * Message belongs to Chat.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chat()
    {
    	// belongsTo(RelatedModel, foreignKey = chat_id, keyOnRelatedModel = id)
    	return $this->belongsTo(Chat::class);
    }

    /**
     * Message belongs to User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        // belongsTo(RelatedModel, foreignKey = user_id, keyOnRelatedModel = id)
        return $this->belongsTo(User::class);
    }

    public function recievers()
    {
        return $this->belongsToMany(User::class)->withPivot('status');
    }

    public function keywords()
    {
    	return $this->belongsToMany(Keyword::class);
    }

    public function getBodyAttribute($value)
    {
        return json_decode($value);
    }
}
