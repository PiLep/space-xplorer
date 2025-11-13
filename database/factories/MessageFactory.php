<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sender_id' => null, // System messages have no sender
            'recipient_id' => User::factory(),
            'type' => fake()->randomElement(['system', 'discovery', 'mission', 'alert', 'welcome']),
            'subject' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'is_read' => false,
            'read_at' => null,
            'is_important' => false,
            'metadata' => null,
        ];
    }

    /**
     * Indicate that the message is read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Indicate that the message is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Indicate that the message is important.
     */
    public function important(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_important' => true,
        ]);
    }

    /**
     * Set the message type.
     */
    public function ofType(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
        ]);
    }

    /**
     * Set the sender (for non-system messages).
     */
    public function from(User $sender): static
    {
        return $this->state(fn (array $attributes) => [
            'sender_id' => $sender->id,
        ]);
    }

    /**
     * Set the recipient.
     */
    public function to(User $recipient): static
    {
        return $this->state(fn (array $attributes) => [
            'recipient_id' => $recipient->id,
        ]);
    }
}
