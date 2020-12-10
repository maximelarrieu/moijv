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
    public function index(Location $location): Response
    {
        return $this->render('location/index.html.twig', [
            'location' => $location,
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
            $location->setOwner($location->getGame()->getUser());

            $manager->persist($location);
            $manager->flush();

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('mailer@moijv.com', 'Moijv Mail Bot'))
                    ->to($game->getUser()->getEmail())
                    ->subject('Une demande de location de votre jeu à été formulé')
                    ->htmlTemplate('location/new_location.html.twig')
                    ->context([
                        'location' => $location,
                        'user' => $user,
                        'game' => $game
                    ])
            );

            return $this->redirectToRoute('message', [
                'id' => $location->getId()
            ]);
        } else {
            return $this->redirectToRoute('game_details', [
                'id' => $game->getId()
            ]);
        }
    }

    /**
     * @Route("location/{id}/accepted", name="accept_location")
     */
    public function accept(Location $location, EntityManagerInterface $manager): Response {
        $user = $this->getUser();
        $location->setStatut('VALIDÉ');

        $locater = $location->getUser();
        $locater->addGame($location->getGame());

        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('mailer@moijv.com', 'Moijv Mail Bot'))
                ->to($locater->getEmail())
                ->subject('Votre demande de location a été ' . $location->getStatut() . ' !')
                ->htmlTemplate('location/response_location.html.twig')
                ->context([
                    'location' => $location
                ])
        );

        $manager->persist($location);
        $manager->persist($locater);
        $manager->persist($user);
        $manager->flush();

        return $this->redirectToRoute('profile');
    }

    /**
     * @Route("location/{id}/refused", name="refuse_location")
     */
    public function refuse(Location $location, EntityManagerInterface $manager): Response {
        $user = $this->getUser();
        $location->setStatut('REFUSÉ');

        $locater = $location->getUser();

        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('mailer@moijv.com', 'Moijv Mail Bot'))
                ->to($locater->getEmail())
                ->subject('Votre demande de location a été ' . $location->getStatut() . ' !')
                ->htmlTemplate('location/response_location.html.twig')
                ->context([
                    'location' => $location
                ])
        );

        $manager->persist($location);
        $manager->flush();

        return $this->redirectToRoute('profile');
    }

    /**
     * @Route("location/{id}/delete", name="delete_location")
     */
    public function delete(Location $location, EntityManagerInterface $manager): Response {
        $manager->remove($location);
        $manager->flush();

        return $this->redirectToRoute('profile');
    }

    /**
     * @Route("location/{id}/return", name="return_location")
     */
    public function returnGame(Location $location, EntityManagerInterface $manager): Response {
        $owner = $location->getOwner();
        $owner->addGame($location->getGame());
        $location->setEndAt(new \DateTime());

        $manager->persist($owner);
        $manager->persist($location);
        $manager->flush();

        return $this->redirectToRoute('profile');
    }
}
