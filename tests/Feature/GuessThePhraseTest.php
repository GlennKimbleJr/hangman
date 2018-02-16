<?php

namespace Tests\Feature;

use App\User;
use App\Phrase;
use Tests\TestCase;
use App\Factories\HangmanFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GuessThePhraseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_guest_cannot_guess()
    {
        $response = $this->post(route('guess-phrase'));

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function a_user_cannot_guess_without_a_game_in_progress()
    {
        $user = factory(User::class)->create();
        $this->assertFalse($user->hasGameInProgress());

        $response = $this->actingAs($user)->post(route('guess-phrase'), [
            'guess' => 'A quick test',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function a_guess_is_required()
    {
        $phrases = factory(Phrase::class, 10)->create();
        $user = factory(User::class)->create();
        HangmanFactory::create($user);

        $response = $this->actingAs($user)->post(route('guess-phrase'), [
            'guess' => null,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('guess');
    }

    /** @test */
    public function a_correct_guess_will_be_stored_as_true()
    {
        $phrases = factory(Phrase::class, 10)->create();
        $user = factory(User::class)->create();
        HangmanFactory::create($user);
        $user = $user->fresh();
        $user->getActiveGame()->getActiveRound()->phrase()->update([
            'text' => 'correct phrase',
        ]);

        $response = $this->actingAs($user)->post(route('guess-phrase'), [
            'guess' => 'correct phrase',
        ]);

        $guess = $user->fresh()->getActiveGame()->getActiveRound()->guesses;
        $this->assertCount(1, $guess);
        $this->assertTrue($guess->first()->is_correct);
    }

    /** @test */
    public function a_incorrect_guess_will_be_stored_as_false()
    {
        $phrases = factory(Phrase::class, 10)->create();
        $user = factory(User::class)->create();
        HangmanFactory::create($user);
        $user = $user->fresh();
        $user->getActiveGame()->getActiveRound()->phrase()->update([
            'text' => 'correct phrase',
        ]);

        $response = $this->actingAs($user)->post(route('guess-phrase'), [
            'guess' => 'incorrect phrase',
        ]);

        $guess = $user->fresh()->getActiveGame()->getActiveRound()->guesses;
        $this->assertCount(1, $guess);
        $this->assertFalse($guess->first()->is_correct);
    }
}
