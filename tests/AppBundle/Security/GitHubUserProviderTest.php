<?php
/**
 * Created by PhpStorm.
 * User: issam
 * Date: 31/10/18
 * Time: 12:02
 */

namespace Tests\AppBundle\Security;

use AppBundle\Entity\User;
use AppBundle\Security\GithubUserProvider;
use PHPUnit\Framework\TestCase;

class GithubUserProviderTest extends TestCase
{
    public function testLoadUserByUsernameReturningAUser()
    {
        $client = $this->getMockBuilder('GuzzleHttp\Client')
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();

        $serializer = $this
            ->getMockBuilder('JMS\Serializer\Serializer')
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this
            ->getMockBuilder('Psr\Http\Message\ResponseInterface')
            ->getMock();
        $client
            ->expects($this->once()) // Nous nous attendons à ce que la méthode get soit appelée une fois
            ->method('get')
            ->willReturn($response)
        ;

        $streamedResponse = $this
            ->getMockBuilder('Psr\Http\Message\StreamInterface')
            ->getMock();
        $response
            ->expects($this->once()) // Nous nous attendons à ce que la méthode getBody soit appelée une fois
            ->method('getBody')
            ->willReturn($streamedResponse);

        $userData = ['login' => 'a login', 'name' => 'user name', 'email' => 'adress@mail.com', 'avatar_url' => 'url to the avatar', 'html_url' => 'url to profile'];
        $serializer
            ->expects($this->once()) // Nous nous attendons à ce que la méthode deserialize soit appelée une fois
            ->method('deserialize')
            ->willReturn([]);

        $githubUserProvider = new GithubUserProvider($client, $serializer);
        $user = $githubUserProvider->loadUserByUsername('xxxx');

        $expectedUser = new User($userData['login'], $userData['name'], $userData['email'], $userData['avatar_url'], $userData['html_url']);

        $this->assertEquals($expectedUser, $user);
        $this->assertEquals('AppBundle\Entity\User', get_class($user));



    }

    //---------pour alleger la memoir apres le test
    public function tearDown()
    {
        $this->client = null;
        $this->serializer = null;
        $this->streamedResponse = null;
        $this->response = null;
    }

}

