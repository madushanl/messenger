<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = [ 'word', 'chat_id' ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Keyword belongs to Chat.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chat()
    {
        // belongsTo(RelatedModel, foreignKey = chat_id, keyOnRelatedModel = id)
        return $this->belongsTo(Chat::class);
    }

    public function messages()
    {
        return $this->belongsToMany(Message::class);
    }
}
