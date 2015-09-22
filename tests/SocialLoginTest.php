<?php

namespace SmallHadronCollider\SocialLogin\Tests\Platforms\Two;

use PHPUnit_Framework_TestCase as TestCase;
use Mockery;

use SmallHadronCollider\SocialLogin\SocialLogin;
use SmallHadronCollider\SocialLogin\Contracts\StorerInterface;

class SocialLoginTest extends TestCase
{
    public function setUp()
    {
        $this->mockStorer = Mockery::mock(StorerInterface::class);
        $this->socialLogin = new SocialLogin([], $this->mockStorer);
    }

    /**
     * @expectedException SmallHadronCollider\SocialLogin\Exceptions\InvalidTokenException
     */
    public function testPlatformFromTokenWithPlatform()
    {
        $this->socialLogin->platformFromToken("code");
    }
}
