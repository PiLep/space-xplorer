<?php

namespace Tests\Unit\Listeners;

use App\Events\UserRegistered;
use App\Listeners\GenerateHomePlanet;
use App\Models\Planet;
use App\Models\User;
use App\Services\PlanetGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class GenerateHomePlanetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the listener generates and assigns a planet to the user.
     */
    public function test_generates_and_assigns_planet_to_user(): void
    {
        $user = User::factory()->create([
            'home_planet_id' => null,
        ]);

        $event = new UserRegistered($user);
        $listener = new GenerateHomePlanet(new PlanetGeneratorService);

        $listener->handle($event);

        // Refresh user to get updated data
        $user->refresh();

        $this->assertNotNull($user->home_planet_id, 'User should have a home planet assigned');

        // Verify planet exists
        $planet = Planet::find($user->home_planet_id);
        $this->assertNotNull($planet, 'Planet should exist in database');
        $this->assertInstanceOf(Planet::class, $planet);
    }

    /**
     * Test that the listener handles errors gracefully without blocking registration.
     */
    public function test_handles_errors_gracefully(): void
    {
        // Create a mock service that throws an exception
        $mockService = Mockery::mock(PlanetGeneratorService::class);
        $mockService->shouldReceive('generate')
            ->once()
            ->andThrow(new \Exception('Test error'));

        $user = User::factory()->create([
            'home_planet_id' => null,
        ]);

        $event = new UserRegistered($user);
        $listener = new GenerateHomePlanet($mockService);

        // Should not throw exception
        $listener->handle($event);

        // User should still exist and home_planet_id should remain null
        $user->refresh();
        $this->assertNull($user->home_planet_id, 'home_planet_id should remain null on error');
    }

    /**
     * Test that successful generation completes without errors.
     */
    public function test_successful_generation_completes_without_errors(): void
    {
        $user = User::factory()->create([
            'home_planet_id' => null,
        ]);

        $event = new UserRegistered($user);
        $listener = new GenerateHomePlanet(new PlanetGeneratorService);

        // Should not throw exception
        $listener->handle($event);

        // Verify planet was assigned
        $user->refresh();
        $this->assertNotNull($user->home_planet_id, 'Planet should be assigned on success');
    }

    /**
     * Test that the assigned planet has all required characteristics.
     */
    public function test_assigned_planet_has_all_characteristics(): void
    {
        $user = User::factory()->create([
            'home_planet_id' => null,
        ]);

        $event = new UserRegistered($user);
        $listener = new GenerateHomePlanet(new PlanetGeneratorService);

        $listener->handle($event);

        $user->refresh();
        $planet = Planet::find($user->home_planet_id);

        $this->assertNotNull($planet->name);
        $this->assertNotNull($planet->type);
        $this->assertNotNull($planet->size);
        $this->assertNotNull($planet->temperature);
        $this->assertNotNull($planet->atmosphere);
        $this->assertNotNull($planet->terrain);
        $this->assertNotNull($planet->resources);
        $this->assertNotNull($planet->description);
    }

    /**
     * Test that multiple users can have different home planets.
     */
    public function test_multiple_users_can_have_different_home_planets(): void
    {
        $user1 = User::factory()->create(['home_planet_id' => null]);
        $user2 = User::factory()->create(['home_planet_id' => null]);

        $listener = new GenerateHomePlanet(new PlanetGeneratorService);

        $listener->handle(new UserRegistered($user1));
        $listener->handle(new UserRegistered($user2));

        $user1->refresh();
        $user2->refresh();

        $this->assertNotNull($user1->home_planet_id);
        $this->assertNotNull($user2->home_planet_id);
        $this->assertNotEquals(
            $user1->home_planet_id,
            $user2->home_planet_id,
            'Users should have different home planets'
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
