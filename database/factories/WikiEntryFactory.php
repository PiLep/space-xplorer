<?php

namespace Database\Factories;

use App\Models\WikiEntry;

/**
 * WikiEntryFactory is an alias for CodexEntryFactory.
 * This factory exists for backward compatibility with tests that use "Wiki" terminology.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WikiEntry>
 */
class WikiEntryFactory extends CodexEntryFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WikiEntry::class;

    // All methods are inherited from CodexEntryFactory
}

