<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegisterType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


/**
 * Class SecurityController
 * @package App\Controller
 * @Route("/user", name="user")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('user/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/register", name="_register")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function register(EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $formUser = $this->createForm(UserRegisterType::class, $user);

        $formUser->handleRequest($request);
        if ($formUser->isSubmitted() && $formUser->isValid()) {
            $user->setDateCreated(new DateTime());
            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'You can now connect with your account !');
            return $this->redirectToRoute('user_login', ['user' => $user]);
        } elseif ($formUser->isSubmitted() && !$formUser->isValid())
            $this->addFlash('error', 'Invalid parameters !');
        return $this->render('user/register.html.twig', [
            "userForm" => $formUser->createView()
        ]);
    }

    /**
     * @Route("/logout", name="_logout")
     */
    public function logout()
    {
    }
}
