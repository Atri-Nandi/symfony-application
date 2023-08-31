<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixtures extends Fixture {

    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager) 
    { 
        $generator = Factory::create();

        // create user
        $adminUser = new User();
        $adminUser->setFirstname('Admin');
        $adminUser->setMiddlename('');
        $adminUser->setLastname('User');
        $adminUser->setEmail('admin_user@gmail.com');
        $adminUser->setRoles(array('ROLE_ADMIN', 'ROLE_USER'));
        $password = $this->hasher->hashPassword($adminUser, 'pass_1234');
        $adminUser->setPassword($password);
        $adminUser->setCountry("Netherlands");
        $manager->persist($adminUser);

        $user = new User();
        $user->setFirstname('Test');
        $user->setMiddlename('');
        $user->setLastname('User');
        $user->setEmail('test_user@gmail.com');
        $user->setRoles(array('ROLE_USER'));
        $password = $this->hasher->hashPassword($user, 'pass_1234');
        $user->setPassword($password);
        $user->setCountry("Germany");
        $manager->persist($user);

        // Add Products
        $category= ['Electronics', 'Fashion', 'Decor', 'Sport'];

        for ($i = 0; $i < 10; $i++) {
            $this->addProduct($generator, $category, $adminUser, $manager);            
        }

        for ($i = 0; $i < 5; $i++) {
            $this->addProduct($generator, $category, $user, $manager);
        }

        // Add Users
        for ($i = 0; $i < 15; $i++) {
            $user = new User();
            $user->setFirstname($generator->firstName);
            $user->setMiddlename("");
            $user->setLastname($generator->lastName);
            $user->setEmail($generator->email);
            $user->setRoles(array('ROLE_USER'));
            $user->setPassword($this->hasher->hashPassword($user, 'test_1234'));
            $user->setPhone($generator->e164PhoneNumber);
            $user->setStreet($generator->streetName);
            $user->setCity($generator->city);
            $user->setHouseNumber($generator->buildingNumber);
            $user->setPostalCode($generator->postcode);
            $user->setCountry($generator->country);
            $manager->persist($user);

            $this->addProduct($generator, $category, $user, $manager);
        }

        $manager->flush();

    }

    /**
     * This function add product to the application
     * 
     * @param $generator Generator object
     * @param array $category Product category
     * @param User $user User object
     * @param ObjectManager $manager Objectmanager
     */
    public function addProduct(
        $generator, 
        array $category, 
        User $user, 
        ObjectManager $manager
    ): void {
        $product = new Product();
        $product->setName($generator->company);
        $product->setDescription($generator->sentence);
        $product->setPrice(mt_rand(1, 2000));
        $product->setCategory($category[rand(0, 3)]);
        $product->setUser($user);
        $product->setCreatedAt(new DateTime());
        $manager->persist($product);
    }
}
