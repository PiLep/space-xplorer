<?php

namespace App\Services;

use App\Models\CodexEntry;
use App\Models\Planet;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Service for managing codex entries and planet naming.
 *
 * This service handles the creation, validation, and management of codex entries
 * for planets in the Codex Stellaris system.
 */
class CodexService
{
    public function __construct(
        private AIDescriptionService $aiDescriptionService
    ) {}

    /**
     * Create a codex entry for a planet with AI-generated description.
     *
     * @param  Planet  $planet  The planet to create an entry for
     * @param  User|null  $discoverer  The user who discovered the planet (optional)
     * @return CodexEntry The created codex entry
     */
    public function createEntryForPlanet(Planet $planet, ?User $discoverer = null): CodexEntry
    {
        // Check if entry already exists
        $existingEntry = CodexEntry::where('planet_id', $planet->id)->first();

        if ($existingEntry) {
            Log::info('Codex entry already exists for planet', [
                'planet_id' => $planet->id,
                'codex_entry_id' => $existingEntry->id,
            ]);

            return $existingEntry;
        }

        // Generate fallback name
        $fallbackName = $this->generateFallbackName($planet);

        // Generate AI description (with fallback if generation fails)
        $description = null;
        try {
            $description = $this->aiDescriptionService->generatePlanetDescription($planet);
        } catch (\Exception $e) {
            Log::warning('Failed to generate AI description for planet, using fallback', [
                'planet_id' => $planet->id,
                'error' => $e->getMessage(),
            ]);
            // Description will remain null, can be generated later
        }

        // Create codex entry
        $entry = CodexEntry::create([
            'planet_id' => $planet->id,
            'fallback_name' => $fallbackName,
            'description' => $description,
            'discovered_by_user_id' => $discoverer?->id,
            'is_named' => false,
            'is_public' => true,
        ]);

        Log::info('Codex entry created for planet', [
            'planet_id' => $planet->id,
            'codex_entry_id' => $entry->id,
            'discoverer_id' => $discoverer?->id,
        ]);

        return $entry;
    }

    /**
     * Generate a fallback name for a planet based on its type.
     *
     * @param  Planet  $planet  The planet to generate a name for
     * @return string The generated fallback name
     */
    public function generateFallbackName(Planet $planet): string
    {
        $config = config('codex.fallback_name');
        $prefix = $config['prefix'] ?? 'Planète';
        $typeMapping = $config['type_mapping'] ?? [];
        $format = $config['format'] ?? '{prefix} {type} #{number}';

        // Get planet type from properties
        $type = $planet->properties?->type ?? 'tellurique';
        $typeLabel = $typeMapping[strtolower($type)] ?? ucfirst($type);

        // Generate a unique number based on planet ID (last 4 characters)
        $number = substr((string) $planet->id, -4);

        // Format the name
        $name = str_replace(
            ['{prefix}', '{type}', '{number}'],
            [$prefix, $typeLabel, $number],
            $format
        );

        return $name;
    }

    /**
     * Validate a planet name according to the rules.
     *
     * @param  string  $name  The name to validate
     *
     * @throws ValidationException If validation fails
     */
    public function validateName(string $name): void
    {
        $rules = config('codex.name_validation');

        // Length validation
        $length = mb_strlen($name);
        if ($length < $rules['min_length']) {
            throw ValidationException::withMessages([
                'name' => "Le nom doit contenir au moins {$rules['min_length']} caractères.",
            ]);
        }

        if ($length > $rules['max_length']) {
            throw ValidationException::withMessages([
                'name' => "Le nom ne peut pas dépasser {$rules['max_length']} caractères.",
            ]);
        }

        // Character validation
        if (! preg_match($rules['allowed_characters'], $name)) {
            throw ValidationException::withMessages([
                'name' => 'Le nom contient des caractères non autorisés. Utilisez uniquement des lettres, chiffres, espaces, tirets et apostrophes.',
            ]);
        }

        // Forbidden words validation (case-insensitive)
        $forbiddenWords = $rules['forbidden_words'] ?? [];
        $nameLower = mb_strtolower($name);

        foreach ($forbiddenWords as $forbiddenWord) {
            if (mb_strpos($nameLower, mb_strtolower($forbiddenWord)) !== false) {
                throw ValidationException::withMessages([
                    'name' => 'Le nom contient un mot interdit.',
                ]);
            }
        }

        // Uniqueness validation
        $existing = CodexEntry::where('name', $name)
            ->orWhere('fallback_name', $name)
            ->exists();

        if ($existing) {
            throw ValidationException::withMessages([
                'name' => 'Ce nom est déjà utilisé par une autre planète.',
            ]);
        }
    }

    /**
     * Name a planet with user-provided name.
     *
     * @param  CodexEntry  $entry  The codex entry to update
     * @param  User  $user  The user naming the planet
     * @param  string  $name  The name to assign
     * @return CodexEntry The updated codex entry
     *
     * @throws ValidationException If validation fails
     */
    public function namePlanet(CodexEntry $entry, User $user, string $name): CodexEntry
    {
        // Check permissions
        if (! $this->canUserNamePlanet($entry, $user)) {
            throw ValidationException::withMessages([
                'name' => 'Vous n\'êtes pas autorisé à nommer cette planète.',
            ]);
        }

        // Validate name
        $this->validateName($name);

        // Update entry
        $entry->update([
            'name' => $name,
            'is_named' => true,
        ]);

        Log::info('Planet named by user', [
            'codex_entry_id' => $entry->id,
            'planet_id' => $entry->planet_id,
            'user_id' => $user->id,
            'name' => $name,
        ]);

        return $entry->fresh();
    }

    /**
     * Check if a user can name a planet.
     *
     * Only the discoverer can name the planet.
     *
     * @param  CodexEntry  $entry  The codex entry
     * @param  User  $user  The user to check
     * @return bool True if user can name the planet
     */
    public function canUserNamePlanet(CodexEntry $entry, User $user): bool
    {
        // Only the discoverer can name the planet
        return $entry->discovered_by_user_id === $user->id;
    }

    /**
     * Check if a user can contribute to a codex entry.
     *
     * User must have explored the planet (for MVP, we check if they have a discovery record).
     *
     * @param  CodexEntry  $entry  The codex entry
     * @param  User  $user  The user to check
     * @return bool True if user can contribute
     */
    public function canUserContribute(CodexEntry $entry, User $user): bool
    {
        // For MVP: Check if user has explored the planet
        // This is a simplified check - in a full implementation, you'd check an exploration log
        // For now, we allow any authenticated user to contribute
        // TODO: Implement proper exploration tracking
        return true;
    }

    /**
     * Get paginated list of codex entries with search filter.
     *
     * @param  array  $filters  Filters (search only)
     * @param  int  $perPage  Number of entries per page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getEntries(array $filters = [], int $perPage = 20)
    {
        $query = CodexEntry::with(['planet.properties', 'discoveredBy'])
            ->public()
            ->discovered()
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('fallback_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Search codex entries by name or fallback name (for autocompletion).
     *
     * @param  string  $query  Search query
     * @param  int  $limit  Maximum number of results
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchEntries(string $query, int $limit = 10)
    {
        return CodexEntry::public()
            ->discovered()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('fallback_name', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get(['id', 'name', 'fallback_name', 'planet_id']);
    }
}

