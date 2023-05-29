<?php

use ASanikovich\LaravelSpatial\Database\Connection;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use ASanikovich\LaravelSpatial\Tests\TestCase;
use Illuminate\Support\Facades\DB;

uses(DatabaseTruncation::class);
uses(TestCase::class)->in(__DIR__);

function isSupportAxisOrder(): bool
{
    return (new Connection())->isSupportAxisOrder(DB::connection());
}
