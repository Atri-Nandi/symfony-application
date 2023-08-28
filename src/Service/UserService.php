<?php

namespace App\Service;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserService 
{
    private $logger;
    private $em;
    private $passwordEncoder;
    private $session;
    public $requestStack;

    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordEncoder,
        RequestStack $requestStack
    ) {
        $this->logger = $logger;
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->session = $requestStack->getSession();
    }

    /**
     * @return type
     */
    public function getClassName()
    {
        return User::class;
    }

    /**
     * Get all the users
     * 
     * @return array List of users
     */
    public function getallUsers(): array
    {
        return $this->em->getRepository(User::class)->findAll();

    }

    /**
     * This function handles the user login
     * 
     * @param string $email    Email address
     * @param string $password Password 
     * 
     * @return User|null User object or null
     */
    public function loginUser(string $email, string $password): ?User
    {
        if(filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
            if(!$user){
                return null;
            }

            if(!$this->checkCredentials($password, $user)){
                return null;
            }
            return $user;
        }
        return null;       
    }

    /**
     * This function check Credentials i.e. password is valid or not
     * 
     * @param string $password Password 
     * @param UserInterface $user User
     * 
     * @return bool True if password is valid
     */
    public function checkCredentials(string $password, UserInterface $user): bool
    {
        return $this->passwordEncoder->isPasswordValid($user, $password);
    }

    /**
     * This function handles the user registration with the email address
     * 
     * @param User $user    User object
     * 
     * @return User User object
     */
    public function createUser(User $user): User
    {
        $existingUser = $this->em->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);

        // User already exists then continue with this user
        if($existingUser){
            $this->logger->info("UseService - user already exists");

            return $existingUser;
        } else {
            $user->setRoles(["ROLE_USER"]);
            $password = $this->passwordEncoder->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setCreatedAt(new DateTime());
            $this->em->persist($user);
            $this->logger->info("UseService - new user created");

            return $user;
        }
    }

    /**
     * Returns the currently logged in user object
     * 
     * @return User|null User object or null
     */
    public function getUser(): ?User 
    {
        if($this->session->has('user-id'))
        {
            $userId = $this->session->get('user-id');
            $user = $this->em->getRepository(User::class)->findOneBy(['id' => $userId]);

            if($user){
                $user->setLastLogin(new DateTime());
                $this->em->persist($user);
                $this->em->flush();
            }

            return $user;            
        } else {
            return null;
        }
    }
}