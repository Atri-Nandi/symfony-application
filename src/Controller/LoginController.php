<?php
namespace App\Controller;

use App\Lib\Trans;
use App\Service\UserService;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginController extends AbstractController
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
     * This is the login page of the website 
     * 
     * @param Request $request Symfony request object
     * 
     * @return Response Symfony Response object
     * 
     * @Route("/login", name="login")
     */
    public function login(Request $request): Response
    {
        $loggedInUser = $this->userService->getUser();

        if ($loggedInUser) {
            return $this->redirectToRoute('dashboard');
        }
        
        // Create form
        $form = $this->getForm();
        $form->handleRequest($request);

        // Handle Submit
        if ($form->isSubmitted()) {
            if ($response = $this->handleLogin($form)) {
                return $response;
            }            
        }

        return $this->render('login.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * Generate Login Form
     * 
     * @return Form Symfony Form object
     */
    private function getForm(): Form
    {
        return $this->createFormBuilder()
            ->add('email', TextType::class, [
                'label' => Trans::tr('user.form.email.label'),
                'constraints' =>[
                    new Email(['message' => 'This is a invalid email address']),
                    new NotBlank(['message' => 'This field can not be blank'])
                ],                
                'attr' => ['class' => 'form-control', 'placeholder' => Trans::tr('user.form.email.placeholder')],                
            ])
            ->add('password', PasswordType::class, [
                'label' => Trans::tr('user.form.password.label'),
                'constraints' =>[
                    new NotBlank(['message' => 'This field can not be blank'])
                ],  
                'attr' => ['class' => 'form-control', 'placeholder' => Trans::tr('user.form.password.placeholder')],                
            ])
            ->add('login', SubmitType::class, [
                'label' => Trans::tr('generic.button.login'),
                'attr' => ['class' => 'btn btn-primary d-grid w-100'],                
            ])
            ->getForm();
    }

    /** 
     * Handle the login button. This checks email and password is a valid user
     * 
     * @param Form $form Symfony form object
     * 
     * @return Response|null Symfony Redirect Response object or null
     */
    private function handleLogin(Form $form): ?Response
    {
        $data = $form->getData();

        if ($form->get('login')->isClicked() && $form->isValid()) {
            $user = $this->userService->loginUser($data['email'], $data['password']);
            if (!$user) {
                $this->addFlash('error', 'User login attempt failéd');

                return $this->redirectToRoute('login');
            } else {
                $this->session->set('user-id', $user->getId());

                return $this->redirectToRoute('dashboard');
            }
        }

        return null;
    }
}

