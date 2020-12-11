<?php

namespace App\Controller;

use App\Entity\Location;
use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ProfileFormType;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     * @Route("/user/{id}", name="user_details")
     */
    public function details(User $user = null, LocationRepository $locationRepository): Response
    {
        $user ??= $this->getUser();
        $locations = $user->getLocations();
        $own = $locationRepository->locationOwnerIsCurrentUser($user);

        if( ! $user) {
            return $this->redirectToRoute('login');
        }

        return $this->render('user/details.html.twig', [
            'user' => $user,
            'locations' => $locations,
            'ownlocations' => $own
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/profile/update", name="profile_update")
     */
    public function updateProfile(Request $request, EntityManagerInterface $manager)
    {
        /** @var UserInterface $user */
        $user = $this->getUser();
        $form = $this->createForm(ProfileFormType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('profile');
        }

        return $this->render('user/update_profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/profile/update-password", name="profile_update_password")
     */
    public function updatePassword(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $form = $this->createForm(ChangePasswordFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $user->setPassword($encoder->encodePassword($user, $form->get('plainPassword')->getData()));
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('profile');
        }

        return $this->render('user/update_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
