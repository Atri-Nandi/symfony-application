<?php
namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductController extends AbstractController
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
     * This is the product page.
     * 
     * @param Request $request Symfony request object
     * @param int     $id      Database ID of the prouct object
     * 
     * @return Response Symfony Reponse object
     * 
     * @Route("/product/{id}", name="product", requirements={"id": "\d+"})
     */
    public function product(Request $request, int $id = 0): Response
    {
        $loggedInUser = $this->userService->getUser();

        if(!$loggedInUser){
            return $this->redirectToRoute('home');
        }

        if(intval($id) !== 0){
            $product = $this->em->getRepository(Product::class)->findOneBy(['id' => $id]);
        } else {
            $product = new Product();
            $product->setUser($loggedInUser);
            $product->setCreatedAt(new DateTime());
        }
       
        // Get the user object
        if(!$product){
            return $this->redirectToRoute('dashboard');
        }

        // Create form
        $form = $this->createForm(ProductType::class, $product,[
            'validation_groups' => ['productVal']
        ]);
        $form->handleRequest($request);

        // Handle Submit
        if($form->isSubmitted()){
            if($response = $this->handleSubmit($request, $form)){
                return $response;                
            }
        }

        return $this->render('product.html.twig',[
            'form' => $form->createView(),
            'user' => $loggedInUser,
            'showUser' => in_array('ROLE_ADMIN', $loggedInUser->getRoles(), true)
        ]);
    }
     
    /** 
     * Handle the submit button. This update the product details.
     * 
     * @param Form $form Symfony form object
     * 
     * @return Response|null Symfony Response object
     */
    private function handleSubmit(Request $request, Form $form): ?Response
    {
        $product = $form->getData();

        if ($form->get('submit')->isClicked() && $form->isValid()) {
            $this->em->persist($product);
            $this->em->flush();

            return $this->redirectToRoute('product-list');
        } elseif ($form->get('cancel')->isClicked()) {
            return $this->redirectToRoute('product-list');
        }

        return null;
    }
}

