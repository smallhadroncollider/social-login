<?php

namespace SmallHadronCollider\SocialLogin\Tests\Platforms\One;

use PHPUnit_Framework_TestCase as TestCase;
use Mockery;
use League\OAuth1\Client\Server\Server;
use League\OAuth1\Client\Credentials\TokenCredentials;
use League\OAuth1\Client\Credentials\TemporaryCredentials;

use SmallHadronCollider\SocialLogin\User;
use SmallHadronCollider\SocialLogin\Contracts\StorerInterface;
use SmallHadronCollider\SocialLogin\Platforms\One\AbstractPlatform;

class AbstractPlatformTest extends TestCase
{
    public function setUp()
    {
        $this->tokenCredentials = Mockery::mock(TokenCredentials::class);
        $this->temporaryCredentials = Mockery::mock(TemporaryCredentials::class);

        $this->mockServer = Mockery::mock(Server::class, [
            "getAuthorizationUrl" => "http://test.com/auth",
            "getTemporaryCredentials" => $this->temporaryCredentials,
            "getTokenCredentials" => $this->tokenCredentials,
            "getUserUid" => 1,
            "getUserScreenName" => "test",
            "getUserEmail" => "test@test.com",
            "getUserDetails" => (object) ["extra" => ["id" => 1, "name" => "test", "other" => "test"]],
        ]);

        $this->mockStorer = Mockery::mock(StorerInterface::class, [
            "store" => true,
        ]);

        $this->platform = (new TestPlatform($this->mockServer))->setStorer($this->mockStorer)->setSessionID(1);
    }

    public function testGetAuthUrl()
    {
        $this->assertEquals("http://test.com/auth", $this->platform->getAuthUrl());
    }

    public function testAuthorizeUser()
    {
        $this->mockStorer->shouldReceive("get")->andReturn(serialize($this->temporaryCredentials));

        $user = $this->platform->authorizeUser("token:verifier");

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(1, $user->id);
        $this->assertEquals("test@test.com", $user->email);
        $this->assertEquals("test", $user->name);
        $this->assertEquals("test", $user->other);
    }

    public function testGetUser()
    {
        $this->mockStorer->shouldReceive("get")->andReturn(serialize($this->tokenCredentials));
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
}
