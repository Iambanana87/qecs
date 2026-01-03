<?php

namespace Tests\Unit\Enums;

use PHPUnit\Framework\TestCase;
use App\Enums\FiveMType;

class FiveMTypeTest extends TestCase
{
    public function test_it_has_all_five_m_types(): void
    {
        $this->assertSame([
            'MAN',
            'MACHINE',
            'METHOD',
            'MATERIAL',
            'ENVIRONMENT',
        ], FiveMType::values());
    }
}
