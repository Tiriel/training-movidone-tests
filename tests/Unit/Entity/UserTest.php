<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Mapping\Loader\AttributeLoader;
use Symfony\Component\Validator\Validation;

class UserTest extends TestCase
{
    /**
     * @dataProvider provideAddress
     */
    public function testEmail(string $email, bool $valid = false): void
    {
        $user = (new User())
            ->setEmail($email);

        $validator = Validation::createValidatorBuilder()
            ->addLoader(new AttributeLoader())
            ->getValidator();
        ;

        $result = $validator->validate($user);
        $this->assertCount($valid ? 0 : 1, $result);
    }

    public static function provideAddress(): \Generator
    {
        yield 'invalid foo@foo' => ['foo@foo', false];
        yield 'invalid foofoo' => ['foofoo', false];
        yield 'invalid foo.foo' => ['foo.foo', false];
        yield 'valid foo@foo.com' => ['foo@foo.com', true];
    }
}
