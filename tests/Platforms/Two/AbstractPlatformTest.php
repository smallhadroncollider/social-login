<?php

namespace SmallHadronCollider\SocialLogin\Tests\Platforms\Two;

use PHPUnit_Framework_TestCase as TestCase;
use Mockery;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;

use SmallHadronCollider\SocialLogin\User;
use SmallHadronCollider\SocialLogin\Contracts\StorerInterface;
use SmallHadronCollider\SocialLogin\Platforms\Two\AbstractPlatform;

class AbstractPlatformTest extends TestCase
{
    public function setUp()
    {
        $this->accessToken = Mockery::mock(AccessToken::class);

        $this->mockProvider = Mockery::mock(AbstractProvider::class, [
            "getAuthorizationUrl" => "http://test.com/auth",
            "getAccessToken" => $this->accessToken,
            "getState" => "state",
            "getResourceOwner" => Mockery::mock([
                "id" => 1,
                "name" => "test",
                "email" => "test@test.com",
                "toArray" => ["id" => 1, "name" => "test", "other" => "test"]
            ]),
        ]);

        $this->mockStorer = Mockery::mock(StorerInterface::class, [
            "store" => true,
        ]);

        $this->platform = (new TestPlatform($this->mockProvider))->setStorer($this->mockStorer)->setSessionID(1);
    }

    public function testGetAuthUrl()
    {
        $this->assertEquals("http://test.com/auth", $this->platform->getAuthUrl());
    }

    public function testAuthorizeUser()
    {
        $this->mockStorer->shouldReceive("get")->andReturn("state");
        $user = $this->platform->authorizeUser("code:state");

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(1, $user->id);
        $this->assertEquals("test@test.com", $user->email);
        $this->assertEquals("test", $user->name);
        $this->assertEquals("test", $user->other);
    }

    /**
     * @expectedException SmallHadronCollider\SocialLogin\Exceptions\InvalidAuthCodeException
     */
    public function testAuthorizeUserInvalidState()
    {
        $this->mockStorer->shouldReceive("get")->andReturn("incorrect");
        $this->platform->authorizeUser("code:state");
    }

    public function testGetUser()
    {
        $this->mockStorer->shouldReceive("get")->andReturn(serialize($this->accessToken));

        $user = $this->platform->getUser(1);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(1, $user->id);
        $this->assertEquals("test@test.com", $user->email);
        $this->assertEquals("test", $user->name);
        $this->assertEquals("test", $user->other);
    }
}

class TestPlatform extends AbstractPlatform
{
    protected $platform = "test";

    protected function getUserID($resourceOwner)
    {
        return $resourceOwner->id();
    }

    protected function getUserName($resourceOwner)
    {
        return $resourceOwner->name();
    }

    protected function getUserEmail($resourceOwner)
    {
        return $resourceOwner->email();
    }
}
