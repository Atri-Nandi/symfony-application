<?php
namespace App\Controller;

use App\Entity\Product;
use App\Service\ProductService;
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

class ProductListController extends AbstractController
{
    public $translator;
    public $logger;
    public $kernel;
    public $managerRegistry;
    public $requestStack;
    public $userService;
    public $productService;
    private $em;

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
     * This is the product list page.
     * 
     * @param Request $request Symfony request object
     * 
     * @return Response Symfony Reponse object
     * 
     * @Route("/product-list", name="product-list")
     */
    public function productList(Request $request): Response
    {
        $loggedInUser = $this->userService->getUser();

        if(!$loggedInUser){
            return $this->redirectToRoute('home');
        }  

        if (in_array('ROLE_ADMIN', $loggedInUser->getRoles(), true)) {
            $productList = $this->productService->getallProducts();        
        } else {
            $productList = $this->productService->getallProductsByUser($loggedInUser); 
        }        

        return $this->render('product_list.html.twig',[
            'productlist' => $productList,
            'user' => $loggedInUser,
            'showUser' => in_array('ROLE_ADMIN', $loggedInUser->getRoles(), true)
        ]);
    }


    /**
     * Delete the product
     * 
     * @param Request $request Symfony request object
     * @param int     $id      Database ID of the product object
     * 
     * @return Response Symfony Reponse object
     * 
     * @Route("/delete-product/{id}", name="delete-product", requirements={"id": "\d+"})
     */
    public function deleteProduct(Request $request, int $id = 0): Response
    {
        $loggedInUser = $this->userService->getUser();

        if(!$loggedInUser){
            return $this->redirectToRoute('home');
        } 
        
        // Get the product object
        $product = $this->em->getRepository(Product::class)->findOneBy(['id' => $id]);
        if(!$product){
            return $this->redirectToRoute('dashboard');
        } else {
            $this->em->remove($product);
            $this->em->flush();

            return $this->redirectToRoute('product-list');
        }
    }
}

