<?php

namespace App\Tests\Unit\Transformer;

use App\Entity\Organization;
use App\Transformer\ApiToOrganizationTransformer;
use PHPUnit\Framework\TestCase;

class ApiToOrganizationTransformerTest extends TestCase
{
    public function testMethodTransformReturnsOrganization(): void
    {
        $data = [
            'name' => 'Test Organization',
            'presentation' => 'Test Presentation',
            'createdAt' => '2025-06-10',
        ];
        $transformer = new ApiToOrganizationTransformer();
        $result = $transformer->transform($data);

        $this->assertInstanceOf(Organization::class, $result);
        $this->assertSame('Test Organization', $result->getName());
    }
}
