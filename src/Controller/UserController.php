<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractController
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
     * @param int     $id      Database ID of the user object
     * 
     * @return Response Symfony Reponse object
     * 
     * @Route("/user/{id}", name="user", requirements={"id": "\d+"})
     */
    public function user(Request $request, int $id = 0): Response
    {
        $loggedInUser = $this->userService->getUser();

        if(!$loggedInUser){
            return $this->redirectToRoute('home');
        }

        // Get the user object
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $id]);
        if(!$user){
            return $this->redirectToRoute('dashboard');
        }

        // Create form
        $form = $this->createForm(AccountType::class, $user,[
            'form_type' => AccountType::USER_ACCOUNT,
            'validation_groups' => ['userVal']
        ]);
        $form->handleRequest($request);

        // Handle Submit
        if($form->isSubmitted()){
            if($response = $this->handleSubmit($request, $form)){
                return $response;                
            }
        }

        return $this->render('user_profile.html.twig',[
            'form' => $form->createView(),
            'user' => $loggedInUser,
            'showUser' => in_array('ROLE_ADMIN', $loggedInUser->getRoles(), true)
        ]);
    }

    /** 
     * Handle the login button. This checks email and password is a valid user
     * 
     * @param Form $form Symfony form object
     * 
     * @return Response|null Symfony Response object
     */
    private function handleSubmit(Request $request, Form $form): ?Response
    {
        $user = $form->getData();

        if($form->get('submit')->isClicked() && $form->isValid()){
            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('user-list');
        } elseif($form->get('cancel')->isClicked()){
            return $this->redirectToRoute('dashboard');
        }

        return null;
    }
}

