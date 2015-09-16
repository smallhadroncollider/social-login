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
        $this->tokenCredentials = new TokenCredentials();
        $this->tokenCredentials->setIdentifier("identifier");
        $this->tokenCredentials->setSecret("secret");

        $this->temporaryCredentials = new TemporaryCredentials();

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
            "clear" => true,
        ]);

        $this->platform = (new TestPlatform($this->mockServer))->setStorer($this->mockStorer)->setSessionID(1);
    }

    public function testGetAuthUrl()
    {
        $this->assertEquals("http://test.com/auth", $this->platform->getAuthUrl());
    }

    public function testGetTokenFromCode()
    {
        $this->mockStorer->shouldReceive("get")->andReturn(serialize($this->temporaryCredentials));
        $token = $this->platform->getTokenFromCode("token:verifier");
        $this->assertEquals("identifier:secret", $token);
    }

    public function testGetUserFromToken()
    {
        $user = $this->platform->getUserFromToken("identifier:secret");

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
