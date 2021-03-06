<?php

namespace Tests\Unit;

use App\Game;
use App\Phrase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PhraseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function for_game_returns_phrases_with_a_letter_count_greater_than_or_equal_to_the_given_number()
    {
        $longEnough = factory(Phrase::class)->create([
            'text' => 'Test phrase.',
        ]);

        $tooShort = factory(Phrase::class)->create([
            'text' => 'aaaa',
        ]);

        $phrases = Phrase::forGame(5, 2);

        $this->assertCount(1, $phrases);
        $this->assertEquals($longEnough->id, $phrases->first()->id);
    }

    /** @test */
    public function for_game_does_not_include_spaces_in_the_letter_count_of_a_phrase()
    {
        $tooShort = factory(Phrase::class)->create([
            'text' => 'aa aa ',
        ]);

        $phrases = Phrase::forGame(5, 1);

        $this->assertEmpty($phrases);
    }

    /** @test */
    public function add_to_game_creates_a_round_for_the_give_game_linked_to_the_phrase()
    {
        $phrase = factory(Phrase::class)->create();
        $game = factory(Game::class)->create();

        $phrase->addToGame($game);

        $rounds = $game->fresh()->rounds;
        $this->assertCount(1, $rounds);
        $this->assertEquals($phrase->id, $rounds->first()->phrase_id);
    }

    /** @test */
    public function the_text_attribute_always_returns_upper_case()
    {
        $phrase = factory(Phrase::class)->create([
            'text' => 'lower case words',
        ]);

        $this->assertEquals('LOWER CASE WORDS', $phrase->text);
        $this->assertNotEquals('lower case words', $phrase->text);
    }
}
