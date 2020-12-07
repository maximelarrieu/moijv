<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Location;
use App\Repository\GameRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

class LocationController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }
    /**
     * @Route("/location/{id}", name="location")
     */
    public function index(): Response
    {
        return $this->render('location/index.html.twig', [
            'controller_name' => 'LocationController',
        ]);
    }

    /**
     * @Route("/location/new/{id}", name="new_location")
     */
    public function newLocation(Game $game, EntityManagerInterface $manager, Request $request): Response {

        $user = $this->getUser();

        if ($game->getLocations()->isEmpty()) {
            $location = new Location();

            $location->setUser($user);
            $location->setGame($game);
            $location->setCreatedAt(new \DateTime());
            $location->setStatut("EN ATTENTE");

            $manager->persist($location);
            $manager->flush();

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('mailer@moijv.com', 'Moijv Mail Bot'))
                    ->to($game->getUser()->getEmail())
                    ->subject('Une demande de location de votre jeu à été formulé')
                    ->htmlTemplate('registration/new_location.html.twig')
            );

            return $this->redirectToRoute('profile');
        } else {
            return $this->redirectToRoute('game_details', [
                'id' => $game->getId()
            ]);
        }
    }
}
