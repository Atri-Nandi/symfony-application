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

class RegisterController extends AbstractController
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
     * This is the account creation page of the website. 
     * 
     * @param Request $request Symfony request object
     * 
     * @return Response Symfony Response object
     * 
     * @Route("/register", name="register")
     */
    public function register(Request $request): Response
    {
        $loggedInUser = $this->userService->getUser();

        if ($loggedInUser) {
            return $this->redirectToRoute('dashboard');
        }
        
        // Create new User
        $user = new User();

        // Create form
        $form = $this->createForm(AccountType::class, $user,[
            'form_type' => AccountType::REGISTER,
            'validation_groups' => ['userVal']
        ]);
        $form->handleRequest($request);

        // Handle Submit
        if ($form->isSubmitted()) {
            if ($response = $this->handleRegister($form)) {
                return $response;
            }          
        }

        return $this->render('register.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /** 
     * Handle the register button. This function create account of the user.
     * 
     * @param Form $form Symfony form object
     * 
     * @return Response|null Symfony Response object
     */
    private function handleRegister(Form $form): ?Response
    {
        $formData = $form->getData();

        if ($form->get('register')->isClicked() && $form->isValid()) {
            $user = $this->userService->createUser($formData);
            if($user){            
                $this->em->flush();
                $this->addFlash('notice', 'Succesfully created account, Login into your account');

                return $this->redirectToRoute('login');
            } else {
                $this->addFlash('error', 'Failed to create account');

                return $this->redirectToRoute('/');
            }
        }
        
        return null;
    }
}

