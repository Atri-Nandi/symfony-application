<?php
namespace App\Tests\Service;

use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserServiceTest extends KernelTestCase
{
    private $userService;
    private $em;


    /**
     * Initilize the user service
     */
    public function init()
    {
        self::bootKernel(['environment' => 'test', 'debug' => true]);
        $this->em = $em = $this->createMock(EntityManagerInterface::class);

        $user = new User();
        $userRepository = $this->createMock(EntityRepository::class);
        $userRepository->expects($this->any())->method('findOneBy')->willReturn($user);
        $em->expects($this->any())->method('getRepository')->willReturn($userRepository);

        $logger = $this->createMock(LoggerInterface::class);
        $requestStack = $this->createMock(RequestStack::class);
        $userpassword = $this->createMock(UserPasswordHasherInterface::class);

        $this->userService = new UserService($logger, $em, $userpassword, $requestStack);        
    }

    /**
     * Test the get user functionality
     */
    public function testgetUser(): void
    { 
       $this->init();
       $result = $this->userService->getUser();
       $this->assertEmpty($result);
    }
}