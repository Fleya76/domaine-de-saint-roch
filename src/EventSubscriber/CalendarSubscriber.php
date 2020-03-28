<?php

namespace App\EventSubscriber;

use App\Repository\BookingRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    private $bookingRepository;
    private $router;

    public function __construct(
        BookingRepository $bookingRepository,
        UrlGeneratorInterface $router
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar)
    {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();

        // Modify the query to fit to your entity and needs
        // Change booking.beginAt by your start date property
        // $bookings = $this->bookingRepository
        //     ->createQueryBuilder('booking')
        //     ->where('booking.beginAt BETWEEN :start and :end OR booking.endAt BETWEEN :start and :end')
        //     ->setParameter('start', $start->format('Y-m-d H:i:s'))
        //     ->setParameter('end', $end->format('Y-m-d H:i:s'))
        //     ->getQuery()
        //     ->getResult()
        // ;

        $bookings = $this->bookingRepository->findAll();

        foreach ($bookings as $booking) {
            // this create the events with your data (here booking data) to fill calendar
            $bookingEvent = new Event(
                $booking->getCategory(),
                $booking->getBeginAt(),
                $booking->getEndAt()// If the end date is null or not defined, a all day event is created.
            );

            /*
             * Add custom options to events
             *
             * For more information see: https://fullcalendar.io/docs/event-object
             * and: https://github.com/fullcalendar/fullcalendar/blob/master/src/core/options.ts
             */
            
            // if ($booking->getCategory() == 'Libre') {
            //     $bookingEvent->setOptions([
            //         'backgroundColor' => '#A6D166',
            //         'borderColor' => '#A6D166',
            //     ]);
            // }
            // else if ($booking->getCategory() == 'Renforcement') {
            //     $bookingEvent->setOptions([
            //         'backgroundColor' => '#7B93D1',
            //         'borderColor' => '#7B93D1',
            //     ]);
            // }
            // else if ($booking->getCategory() == 'Théorie techniques d\'apprentissage') {
            //     $bookingEvent->setOptions([
            //         'backgroundColor' => '#648534',
            //         'borderColor' => '#648534',
            //     ]);
            // }
            // else if ($booking->getCategory() == 'Mise en pratique') {
            //     $bookingEvent->setOptions([
            //         'backgroundColor' => '#D16952',
            //         'borderColor' => '#D16952',
            //     ]);
            // }
            // else if ($booking->getCategory() == 'Refus d\'appât') {
            //     $bookingEvent->setOptions([
            //         'backgroundColor' => '#85483A',
            //         'borderColor' => '#85483A',
            //     ]);
            // }
            // else if ($booking->getCategory() == 'Formation théorique sur développement relationnel et étudactif au sein du foyer') {
            //     $bookingEvent->setOptions([
            //         'backgroundColor' => '#8A71D1',
            //         'borderColor' => '#8A71D1',
            //     ]);
            // }
            // else if ($booking->getCategory() == 'Divers') {
            //     $bookingEvent->setOptions([
            //         'backgroundColor' => '#853451',
            //         'borderColor' => '#853451',
            //     ]);
            // }else{
            //     $bookingEvent->setOptions([
            //         'backgroundColor' => 'black',
            //         'borderColor' => 'black',
            //     ]);
            // }

            if ($booking->getPlace() == 'Clinique vétérinaire') {
                $bookingEvent->setOptions([
                    'backgroundColor' => '#7B93D1',
                    'borderColor' => '#7B93D1',
                ]);
            }
            else if ($booking->getPlace() == 'Domaine de Saint-Roch') {
                $bookingEvent->setOptions([
                    'backgroundColor' => '#A6D166',
                    'borderColor' => '#A6D166',
                ]);
            }
            else if ($booking->getPlace() == 'Extérieure') {
                $bookingEvent->setOptions([
                    'backgroundColor' => '#D16952',
                    'borderColor' => '#D16952',
                ]);
            }
            else{
                $bookingEvent->setOptions([
                    'backgroundColor' => 'black',
                    'borderColor' => 'black',
                ]);
            }
            $bookingEvent->addOption(
                'url',
                $this->router->generate('booking_show', [
                    'id' => $booking->getId(),
                ])
            );
            // finally, add the event to the CalendarEvent to fill the calendar
            $calendar->addEvent($bookingEvent);
        }
    }
}