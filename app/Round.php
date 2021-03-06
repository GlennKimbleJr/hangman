<?php

namespace App;

use App\Game;
use App\Guess;
use App\Phrase;
use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    protected $guarded = [];

    protected $dates = ['completed_at'];

    protected $casts = [
        'won' => 'boolean',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function phrase()
    {
        return $this->belongsTo(Phrase::class);
    }

    public function guesses()
    {
        return $this->hasMany(Guess::class);
    }

    public function isComplete()
    {
        return (bool) $this->completed_at;
    }

    public function maxGuessesReached($maxNumber)
    {
        return $this->guesses()->incorrect()->count() >= $maxNumber;
    }

    public function getIncorretGuesses()
    {
        return $this->guesses()->incorrect()->get();
    }

    public function allLettersGuessed()
    {
        return strpos($this->game->getDisplayPhrase(), '_') === false;
    }

    public function markAsLost()
    {
        $this->update([
            'won' => false,
            'completed_at' => now(),
        ]);
    }

    public function markAsWon()
    {
        $this->update([
            'won' => true,
            'completed_at' => now(),
        ]);
    }
}
