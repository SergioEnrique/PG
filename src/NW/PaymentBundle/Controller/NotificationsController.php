<?php
namespace NW\PaymentBundle\Controller;

use NW\PaymentBundle\Entity\NotificationDetails;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Doctrine\ORM\EntityRepository;

class NotificationsController extends Controller
{
    public function listAction()
    {
        $query = $this->getNotificationDetailsRepository()->createQueryBuilder('n')
            ->setMaxResults(20)
            ->addOrderBy('n.createdAt', 'DESC')
            
            ->getQuery()
        ;

        $notifications = array();
        foreach ($query->getResult() as $notification) {
            /** @var NotificationDetails $notification */
            $notifications[] = array(
                'paymentName' => $notification->getPaymentName(),
                'details' => var_export($notification->getDetails(), true),
                'createdAt' => $notification->getCreatedAt(),
            );
        }

        return $this->render('NWPaymentBundle:Notifications:list.html.twig', array(
            'notifications' => $notifications
        ));
    }

    /**
     * @return EntityRepository
     */
    protected function getNotificationDetailsRepository()
    {
        return $this->getDoctrine()->getRepository('NW\PaymentBundle\Entity\NotificationDetails');
    }
}