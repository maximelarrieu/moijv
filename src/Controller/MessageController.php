<?php

namespace App\Controller;

use App\Entity\Location;
use App\Entity\Message;
use App\Form\MessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class MessageController extends AbstractController
{
    /**
     * @Route("/message/location/{id}", name="message")
     * @Security("user == location.getOwner() or user == location.getUser()")
     *
     */
    public function index(Location $location, Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setCreatedAt(new \DateTime());
            $message->setLocation($location);
            $message->setSender($user);
            $manager->persist($message);
            $manager->flush();

            return $this->redirectToRoute('message', [
                'id' => $location->getId(),
            ]);
        }
        return $this->render('message/index.html.twig', [
            'location' => $location,
            'form' => $form->createView()
        ]);
    }
}
