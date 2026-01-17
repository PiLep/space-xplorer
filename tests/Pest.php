<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Feature tests: besoin de migrations complètes (RefreshDatabase)
uses(TestCase::class, RefreshDatabase::class)->in('Feature');

// Unit tests: transactions suffisent (plus rapide, pas besoin de migrations complètes)
uses(TestCase::class, DatabaseTransactions::class)->in('Unit');

// Browser tests: besoin de migrations complètes (RefreshDatabase)
uses(TestCase::class, RefreshDatabase::class)->in('Browser');
