<?php
namespace App\Controller;

use App\Service\UserService;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomeController extends AbstractController
{
    public $translator;
    public $logger;
    public $kernel;
    public $managerRegistry;
    public $requestStack;
    public $userService;
    public $session;

    public function __construct(
        TranslatorInterface $translator,
        LoggerInterface $logger,
        KernelInterface $kernel,
        ManagerRegistry $managerRegistry,
        RequestStack $requestStack,
        UserService $userService
    ) {
        $this->translator=$translator;
        $this->logger=$logger;
        $this->kernel=$kernel;
        $this->managerRegistry=$managerRegistry;
        $this->requestStack = $requestStack;
        $this->userService = $userService;  
        $this->session = $requestStack->getSession();      
    }
    
    /**
     * This is the home page of the website 
     * 
     * @param Request $request Symfony request object
     * 
     * @return Response Symfony Response object
     * 
     * @Route("/", name="home")
     */
    public function home(Request $request): Response
    {
        $loggedInUser = $this->userService->getUser();

        if ($loggedInUser) {
            return $this->redirectToRoute('dashboard');
        }

       return $this->render('index.html.twig');
    }

    /**
     * This function logout from the application
     * 
     * @param Request $request Symfony request object
     * 
     * @return Response Symfony Response object
     * 
     * @Route("/logout", name="logout")
     */
    public function logout(Request $request): Response
    {
        $loggedInUser = $this->userService->getUser();

        if ($loggedInUser) {
            $this->session->remove('user-id');
        }

        return $this->redirectToRoute('login');
    }
}

