<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{   
    /**
     * @Route("/booking", name="booking_index", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY') and is_granted('ROLE_SUBSCRIBER')", message="Vous devez être identifier et valider par l'administration pour voir les prochains cours")
     */
    public function index(BookingRepository $bookingRepository): Response
    {
       
        return $this->render('booking/calendar.html.twig', [
            'bookings' => $bookingRepository->findAll(),
        ]);
    }

    /**
     * @Route("/booking/overview", name="booking_overview", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function overview(BookingRepository $bookingRepository): Response
    {
        return $this->render('booking/overview.html.twig', [
            'bookings' => array_reverse($bookingRepository->findAll()),
        ]);
    }

    /**
     * @Route("/booking/new", name="booking_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(Request $request): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($booking);
            $entityManager->flush();

            return $this->redirectToRoute('booking_index');
        }

        return $this->render('booking/new.html.twig', [
            'booking' => $booking,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/booking/{id}", name="booking_show", methods={"GET"})
     */
    public function show(Booking $booking): Response
    {
        dump($booking);
        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
            'dateTime' => new DateTime()
        ]);
    }

    /**
     * @Route("/booking/{id}/edit", name="booking_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function edit(Request $request, Booking $booking): Response
    {
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('booking_index');
        }

        return $this->render('booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/booking/{id}", name="booking_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Request $request, Booking $booking): Response
    {
        if ($this->isCsrfTokenValid('delete'.$booking->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($booking);
            $entityManager->flush();
        }

        return $this->redirectToRoute('booking_index');
    }

    /**
     * @Route("/booking/{id}/{user}", name="booking_reservation")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function reservation(Booking $booking, User $user)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $booking->addUser($user);
        $entityManager->persist($booking);
        $entityManager->flush();
        return $this->redirectToRoute('booking_index');
    }

    // TODO: Sécuriser les routes pour que personnes d'autre mise à part l'admin et la personne identifié et auteur de l'action puisse intervenir

    /**
     * @Route("/{id}/booking/reservation", name="booking_by_user", methods={"GET"})
     * @Security("user===userConnect and is_granted('ROLE_SUBSCRIBER') or is_granted('ROLE_ADMIN')", message="Vous devez être identifier et valider par l'administration pour voir les prochains cours")
     */
    public function bookingFindByUser(User $userConnect): Response
    {
        // TODO: Afficher uniquement les bookings à venir
        return $this->render('booking/reservation.html.twig', [
            'bookings' => $userConnect->getBookings(),
        ]);
    }

    /**
     * @Route("/{id}/booking/{userConnect}/remove", name="booking_remove_user", methods={"GET"})
     * @Security("user===userConnect and is_granted('ROLE_SUBSCRIBER')", message="Vous devez être identifier et valider par l'administration pour voir les prochains cours")
     */
    public function bookingRemoveByUser(User $userConnect, Booking $booking): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $booking->removeUser($userConnect);
        $entityManager->persist($booking);
        $entityManager->flush();
        return $this->redirectToRoute('booking_by_user', array(
            'id' => $userConnect->getId()
        ));
    }

}