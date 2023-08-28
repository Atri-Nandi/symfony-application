<?php
namespace App\Controller;

use App\Entity\Product;
use App\Service\ProductService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DashboardController extends AbstractController
{
    public $translator;
    public $logger;
    public $kernel;
    public $managerRegistry;
    public $requestStack;
    public $userService;
    public $productService;
    public $em;

    public function __construct(
        TranslatorInterface $translator,
        LoggerInterface $logger,
        KernelInterface $kernel,
        ManagerRegistry $managerRegistry,
        RequestStack $requestStack,
        UserService $userService,
        ProductService $productService,
        EntityManagerInterface $em
    ) {
        $this->translator=$translator;
        $this->logger=$logger;
        $this->kernel=$kernel;
        $this->managerRegistry=$managerRegistry;
        $this->requestStack = $requestStack;
        $this->userService = $userService; 
        $this->productService = $productService; 
        $this->em = $em;  
    }

    
    /**
     * This is the user dashboard page of the website 
     * 
     * @param Request $request Symfony request object
     * 
     * @return Response Symfony object
     * 
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard(Request $request): Response
    {
        $loggedInUser = $this->userService->getUser();

        if (!$loggedInUser) {
            return $this->redirectToRoute('home');
        }

        $userList = $this->userService->getallUsers(); 

        if (in_array('ROLE_ADMIN', $loggedInUser->getRoles(), true)) {
            $products = $this->em->getRepository(Product::class)->getProductWithCategory();
        } else {
            $products = $this->em->getRepository(Product::class)->getProductWithCategory($loggedInUser);
        }    

        return $this->render('dashboard.html.twig',[
            'user' => $loggedInUser,
            'totalUser' => count($userList),
            'userlist' => $userList,
            'products' => $products,
            'showUser' => in_array('ROLE_ADMIN', $loggedInUser->getRoles(), true)
        ]);
    }
}

