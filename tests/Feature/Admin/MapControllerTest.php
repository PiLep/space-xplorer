<?php

use App\Models\Planet;
use App\Models\StarSystem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->password = 'password123';
    $this->admin = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make($this->password),
        'is_super_admin' => true,
    ]);

    config(['admin.email_whitelist' => '']);
    Auth::guard('admin')->login($this->admin);
});

describe('index - Universe Map', function () {
    it('displays the universe map', function () {
        $response = $this->get('/admin/map');

        $response->assertStatus(200)
            ->assertViewIs('admin.map');
    });

    it('passes systems data to the view', function () {
        $system1 = StarSystem::factory()->create([
            'name' => 'Alpha Centauri',
            'x' => 100.50,
            'y' => 200.75,
            'z' => 300.25,
            'star_type' => 'G-type',
            'planet_count' => 3,
            'discovered' => true,
        ]);

        $system2 = StarSystem::factory()->create([
            'name' => 'Beta Centauri',
            'x' => 150.00,
            'y' => 250.00,
            'z' => 350.00,
            'star_type' => 'M-type',
            'planet_count' => 5,
            'discovered' => false,
        ]);

        $response = $this->get('/admin/map');

        $response->assertStatus(200)
            ->assertViewHas('systems');

        $systems = $response->viewData('systems');
        expect($systems)->toHaveCount(2);

        // Check first system data structure
        $firstSystem = $systems->first();
        expect($firstSystem)
            ->toHaveKeys(['id', 'name', 'x', 'y', 'z', 'star_type', 'planet_count', 'discovered'])
            ->and($firstSystem['name'])->toBeIn(['Alpha Centauri', 'Beta Centauri'])
            ->and($firstSystem['x'])->toBeFloat()
            ->and($firstSystem['y'])->toBeFloat()
            ->and($firstSystem['z'])->toBeFloat()
            ->and($firstSystem['discovered'])->toBeBool();
    });

    it('converts coordinates to float', function () {
        $system = StarSystem::factory()->create([
            'x' => '100.50',
            'y' => '200.75',
            'z' => '300.25',
        ]);

        $response = $this->get('/admin/map');

        $systems = $response->viewData('systems');
        $systemData = $systems->firstWhere('id', $system->id);

        expect($systemData['x'])->toBeFloat()->toBe(100.50)
            ->and($systemData['y'])->toBeFloat()->toBe(200.75)
            ->and($systemData['z'])->toBeFloat()->toBe(300.25);
    });

    it('converts discovered to boolean', function () {
        StarSystem::factory()->create(['discovered' => true]);
        StarSystem::factory()->create(['discovered' => false]);
        StarSystem::factory()->create(['discovered' => 1]);
        StarSystem::factory()->create(['discovered' => 0]);

        $response = $this->get('/admin/map');

        $systems = $response->viewData('systems');
        expect($systems->every(fn ($s) => is_bool($s['discovered'])))->toBeTrue();
    });

    it('returns empty collection when no systems exist', function () {
        $response = $this->get('/admin/map');

        $response->assertStatus(200);
        $systems = $response->viewData('systems');
        expect($systems)->toHaveCount(0);
    });

    it('requires authentication', function () {
        Auth::guard('admin')->logout();

        $response = $this->get('/admin/map');

        $response->assertRedirect();
    });

    it('requires super admin privileges', function () {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make($this->password),
            'is_super_admin' => false,
        ]);

        Auth::guard('admin')->login($user);

        $response = $this->get('/admin/map');

        $response->assertRedirect(route('admin.login'));
    });
});

describe('list - Star Systems List', function () {
    it('displays the star systems list', function () {
        $response = $this->get('/admin/systems');

        $response->assertStatus(200)
            ->assertViewIs('admin.systems.index');
    });

    it('passes paginated star systems to the view', function () {
        StarSystem::factory()->count(25)->create();

        $response = $this->get('/admin/systems');

        $response->assertStatus(200)
            ->assertViewHas('starSystems');

        $starSystems = $response->viewData('starSystems');
        expect($starSystems)->toBeInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class)
            ->and($starSystems->count())->toBe(20); // Paginated to 20 per page
    });

    it('includes planet count for each system', function () {
        $system = StarSystem::factory()->create(['planet_count' => 5]);
        Planet::factory()->count(5)->create(['star_system_id' => $system->id]);

        $response = $this->get('/admin/systems');

        $starSystems = $response->viewData('starSystems');
        $foundSystem = $starSystems->firstWhere('id', $system->id);

        expect($foundSystem)->not->toBeNull()
            ->and($foundSystem->planets_count)->toBe(5);
    });

    it('orders systems by latest first', function () {
        $oldSystem = StarSystem::factory()->create(['created_at' => now()->subDays(5)]);
        $newSystem = StarSystem::factory()->create(['created_at' => now()]);

        $response = $this->get('/admin/systems');

        $starSystems = $response->viewData('starSystems');
        $newSystemIndex = $starSystems->search(function ($system) use ($newSystem) {
            return $system->id === $newSystem->id;
        });
        $oldSystemIndex = $starSystems->search(function ($system) use ($oldSystem) {
            return $system->id === $oldSystem->id;
        });

        expect($newSystemIndex)->toBeLessThan($oldSystemIndex);
    });

    it('paginates correctly', function () {
        StarSystem::factory()->count(45)->create();

        $response = $this->get('/admin/systems');

        $starSystems = $response->viewData('starSystems');
        expect($starSystems->total())->toBe(45)
            ->and($starSystems->perPage())->toBe(20)
            ->and($starSystems->currentPage())->toBe(1);
    });

    it('requires authentication', function () {
        Auth::guard('admin')->logout();

        $response = $this->get('/admin/systems');

        $response->assertRedirect();
    });

    it('requires super admin privileges', function () {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make($this->password),
            'is_super_admin' => false,
        ]);

        Auth::guard('admin')->login($user);

        $response = $this->get('/admin/systems');

        $response->assertRedirect(route('admin.login'));
    });
});

describe('show - Star System Map', function () {
    it('displays the star system map', function () {
        $starSystem = StarSystem::factory()->create();

        $response = $this->get("/admin/systems/{$starSystem->id}/map");

        $response->assertStatus(200)
            ->assertViewIs('admin.system-map');
    });

    it('passes star system data to the view', function () {
        $starSystem = StarSystem::factory()->create([
            'name' => 'Alpha Centauri',
            'star_type' => 'G-type',
            'planet_count' => 3,
            'x' => 100.50,
            'y' => 200.75,
            'z' => 300.25,
        ]);

        $response = $this->get("/admin/systems/{$starSystem->id}/map");

        $response->assertStatus(200)
            ->assertViewHas('starSystem')
            ->assertViewHas('planets');

        $systemData = $response->viewData('starSystem');
        expect($systemData)
            ->toHaveKeys(['id', 'name', 'star_type', 'planet_count', 'x', 'y', 'z'])
            ->and($systemData['name'])->toBe('Alpha Centauri')
            ->and($systemData['star_type'])->toBe('G-type')
            ->and($systemData['planet_count'])->toBe(3)
            ->and($systemData['x'])->toBeFloat()->toBe(100.50)
            ->and($systemData['y'])->toBeFloat()->toBe(200.75)
            ->and($systemData['z'])->toBeFloat()->toBe(300.25);
    });

    it('passes planets data with correct structure', function () {
        $starSystem = StarSystem::factory()->create();
        $planet1 = Planet::factory()->create([
            'star_system_id' => $starSystem->id,
            'name' => 'Planet A',
            'orbital_distance' => 10.5,
            'orbital_angle' => 45.25,
            'orbital_inclination' => 5.0,
            'x' => 100.0,
            'y' => 200.0,
            'z' => 300.0,
        ]);
        $planet2 = Planet::factory()->create([
            'star_system_id' => $starSystem->id,
            'name' => 'Planet B',
            'orbital_distance' => null,
            'orbital_angle' => null,
            'orbital_inclination' => null,
            'x' => null,
            'y' => null,
            'z' => null,
        ]);

        $response = $this->get("/admin/systems/{$starSystem->id}/map");

        $planets = $response->viewData('planets');
        expect($planets)->toHaveCount(2);

        // Check first planet structure
        $firstPlanet = $planets->firstWhere('id', $planet1->id);
        expect($firstPlanet)
            ->toHaveKeys(['id', 'name', 'orbital_distance', 'orbital_angle', 'orbital_inclination', 'x', 'y', 'z', 'type', 'size', 'has_image', 'has_video'])
            ->and($firstPlanet['name'])->toBe('Planet A')
            ->and($firstPlanet['orbital_distance'])->toBeFloat()->toBe(10.5)
            ->and($firstPlanet['orbital_angle'])->toBeFloat()->toBe(45.25)
            ->and($firstPlanet['orbital_inclination'])->toBeFloat()->toBe(5.0)
            ->and($firstPlanet['x'])->toBeFloat()->toBe(100.0)
            ->and($firstPlanet['y'])->toBeFloat()->toBe(200.0)
            ->and($firstPlanet['z'])->toBeFloat()->toBe(300.0);

        // Check second planet with null values
        $secondPlanet = $planets->firstWhere('id', $planet2->id);
        expect($secondPlanet['orbital_distance'])->toBeNull()
            ->and($secondPlanet['orbital_angle'])->toBeNull()
            ->and($secondPlanet['orbital_inclination'])->toBeNull()
            ->and($secondPlanet['x'])->toBeNull()
            ->and($secondPlanet['y'])->toBeNull()
            ->and($secondPlanet['z'])->toBeNull();
    });

    it('includes planet properties (type, size)', function () {
        $starSystem = StarSystem::factory()->create();
        $planet = Planet::factory()->create(['star_system_id' => $starSystem->id]);

        // Planet factory automatically creates properties, so we can use them
        $planet->refresh(); // Ensure properties are loaded
        expect($planet->properties)->not->toBeNull();

        $response = $this->get("/admin/systems/{$starSystem->id}/map");

        $planets = $response->viewData('planets');
        $planetData = $planets->firstWhere('id', $planet->id);

        expect($planetData['type'])->not->toBeNull()
            ->and($planetData['size'])->not->toBeNull()
            ->and($planetData['type'])->toBe($planet->properties->type)
            ->and($planetData['size'])->toBe($planet->properties->size);
    });

    it('includes has_image and has_video flags', function () {
        $starSystem = StarSystem::factory()->create();
        $planetWithImage = Planet::factory()->create([
            'star_system_id' => $starSystem->id,
            'image_url' => 'planets/image.jpg',
            'image_generating' => false,
        ]);
        $planetWithVideo = Planet::factory()->create([
            'star_system_id' => $starSystem->id,
            'video_url' => 'planets/video.mp4',
            'video_generating' => false,
        ]);
        $planetGenerating = Planet::factory()->create([
            'star_system_id' => $starSystem->id,
            'image_url' => 'planets/image.jpg',
            'image_generating' => true,
        ]);

        // Mock Storage to make hasImage/hasVideo return true
        \Illuminate\Support\Facades\Storage::fake('s3');
        \Illuminate\Support\Facades\Storage::disk('s3')->put('planets/image.jpg', 'fake');
        \Illuminate\Support\Facades\Storage::disk('s3')->put('planets/video.mp4', 'fake');

        $response = $this->get("/admin/systems/{$starSystem->id}/map");

        $planets = $response->viewData('planets');
        $planetWithImageData = $planets->firstWhere('id', $planetWithImage->id);
        $planetWithVideoData = $planets->firstWhere('id', $planetWithVideo->id);
        $planetGeneratingData = $planets->firstWhere('id', $planetGenerating->id);

        expect($planetWithImageData['has_image'])->toBeTrue()
            ->and($planetWithVideoData['has_video'])->toBeTrue()
            ->and($planetGeneratingData['has_image'])->toBeFalse();
    });

    it('returns 404 for non-existent star system', function () {
        $response = $this->get('/admin/systems/non-existent-id/map');

        $response->assertStatus(404);
    });

    it('only includes planets from the specified star system', function () {
        $starSystem1 = StarSystem::factory()->create();
        $starSystem2 = StarSystem::factory()->create();

        Planet::factory()->count(3)->create(['star_system_id' => $starSystem1->id]);
        Planet::factory()->count(5)->create(['star_system_id' => $starSystem2->id]);

        $response = $this->get("/admin/systems/{$starSystem1->id}/map");

        $planets = $response->viewData('planets');
        expect($planets)->toHaveCount(3)
            ->and($planets->every(fn ($p) => $p['id'] !== null))->toBeTrue();
    });

    it('requires authentication', function () {
        $starSystem = StarSystem::factory()->create();
        Auth::guard('admin')->logout();

        $response = $this->get("/admin/systems/{$starSystem->id}/map");

        $response->assertRedirect();
    });

    it('requires super admin privileges', function () {
        $starSystem = StarSystem::factory()->create();
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make($this->password),
            'is_super_admin' => false,
        ]);

        Auth::guard('admin')->login($user);

        $response = $this->get("/admin/systems/{$starSystem->id}/map");

        $response->assertRedirect(route('admin.login'));
    });
});

