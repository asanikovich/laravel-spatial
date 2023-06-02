<?php

use ASanikovich\LaravelSpatial\Database\Connection;
use ASanikovich\LaravelSpatial\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Support\Facades\DB;

uses(TestCase::class)->in(__DIR__);

function isSupportAxisOrder(): bool
{
    return (new Connection())->isSupportAxisOrder(DB::connection());
}

/**
 * @return class-string
 */
function getDatabaseTruncationClass(): string
{
    return DatabaseTruncation::class;
}
