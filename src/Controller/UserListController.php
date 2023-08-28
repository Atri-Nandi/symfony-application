<?php
namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserListController extends AbstractController
{
    public $translator;
    public $logger;
    public $kernel;
    public $managerRegistry;
    public $requestStack;
    public $userService;
    private $em;

    public function __construct(
        TranslatorInterface $translator,
        LoggerInterface $logger,
        KernelInterface $kernel,
        ManagerRegistry $managerRegistry,
        RequestStack $requestStack,
        UserService $userService,
        EntityManagerInterface $em
    ) {
        $this->translator=$translator;
        $this->logger=$logger;
        $this->kernel=$kernel;
        $this->managerRegistry=$managerRegistry;
        $this->requestStack = $requestStack;
        $this->userService = $userService; 
        $this->em = $em;       
    }
        
    /**
     * This is the user profile page.
     * 
     * @param Request $request Symfony request object
     * 
     * @return Response Symfony Reponse object
     * 
     * @Route("/user-list", name="user-list")
     */
    public function userList(Request $request): Response
    {
        $loggedInUser = $this->userService->getUser();

        if(!$loggedInUser){
            return $this->redirectToRoute('home');
        }
        
        if (!in_array('ROLE_ADMIN', $loggedInUser->getRoles(), true)) {
            return $this->redirectToRoute('dashboard');
        }
        
        $userList = $this->userService->getallUsers();        

        return $this->render('user_list.html.twig',[
            'userlist' => $userList,
            'user' => $loggedInUser,
            'showUser' => in_array('ROLE_ADMIN', $loggedInUser->getRoles(), true)
        ]);
    }


    /**
     * Delete the user
     * 
     * @param Request $request Symfony request object
     * @param int     $id      Database ID of the user object
     * 
     * @return Response Symfony Reponse object
     * 
     * @Route("/delete-user/{id}", name="delete-user", requirements={"id": "\d+"})
     */
    public function deleteUser(Request $request, int $id = 0): Response
    {
        $loggedInUser = $this->userService->getUser();

        if(!$loggedInUser){
            return $this->redirectToRoute('home');
        } 
        
        // Get the user object
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $id]);
        if(!$user){
            return $this->redirectToRoute('dashboard');
        } else {
            foreach($user->getProducts() as $p){
                $p->setUser(null);
            }
            $this->em->remove($user);
            $this->em->flush();

            return $this->redirectToRoute('user-list');
        }
    }
}

