<?php
namespace NW\PaymentBundle\Controller;

use Payum\Bundle\PayumBundle\Controller\PayumController;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Model\OrderInterface;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Request\Sync;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use NW\PrincipalBundle\Entity\cosasRegaladas;

class DetailsController extends PayumController
{
    public function viewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //$cosasRegaladasRepository = $em->getRepository('NWPrincipalBundle:cosasRegaladas');

        // Seguridad, token y estado
        $token = $this->getHttpRequestVerifier()->verify($request);
        
        $payment = $this->getPayum()->getPayment($token->getPaymentName());

        try {
            $payment->execute(new Sync($token));
        } catch (RequestNotSupportedException $e) {}
        
        $payment->execute($status = new GetHumanStatus($token));

        // Qué hacer según el estado
        if ($status->isCaptured()) {

            $details = iterator_to_array($status->getModel());

            $em = $this->getDoctrine()->getEntityManager();

            // Se tiene que iterar cada regalo comprado
            $cantidadDeRegalos = $details["PAYMENTREQUEST_0_CUSTOM"];
            for ($i=0; $i < $cantidadDeRegalos; $i++) {

                // Para cada uno se multiplica la cantidad por el precio del regalo
                $cantidad = $details["L_PAYMENTREQUEST_0_QTY".$i];
                $precio = $details["L_PAYMENTREQUEST_0_AMT".$i];
                $amount = $cantidad * $precio;

                // Array que se usa en los renders para mandar los correos
                $details['regalos'][$i]['nombre'] = $details["L_PAYMENTREQUEST_0_NAME".$i];
                $details['regalos'][$i]['cantidad'] = $cantidad;
                $details['regalos'][$i]['precio'] = $precio;

                // Antes se hacian operaciones aqui
                $aumentoSaldo = $amount;

                // Se obtiene el usuario dueño del objeto
                $itemId = $details["L_PAYMENTREQUEST_0_NUMBER".$i];
                
                $mesaRegalosEntity = $em->getRepository('NWPrincipalBundle:MesaRegalos');
                $regaloObject = $mesaRegalosEntity->find($itemId);
                $usuarioObject = $regaloObject->getBucketGift()->getUser();

                // Se actualiza el regalo con las nuevas partes compradas
                $horcruxesPagadosAnterior = $regaloObject->getHorcruxesPagados();
                $regaloObject->setHorcruxesPagados($horcruxesPagadosAnterior+$cantidad);

                // Se obtiene el saldo del usuario y se le suma la cantidad final
                $saldoViejo = $usuarioObject->getSaldo();
                $saldoNuevo = $saldoViejo + $aumentoSaldo;
                $usuarioObject->setSaldo($saldoNuevo);

                // Registar cada regalo en cosas regaladas
                $regaloRegaladoObject = new cosasRegaladas();
                $regaloRegaladoObject->setRegaladorName($details["FIRSTNAME"]." ".$details["LASTNAME"]);
                $regaloRegaladoObject->setRegaladorMail($details["EMAIL"]);
                $regaloRegaladoObject->setCantidad($cantidad);
                $regaloRegaladoObject->setAmount($aumentoSaldo);
                $regaloRegaladoObject->setUser($usuarioObject);
                $regaloRegaladoObject->setRegalo($regaloObject);

                // Se persiste el usuario, el regalo y el regalo regalado
                $em->persist($usuarioObject);
                $em->persist($regaloObject);
                $em->persist($regaloRegaladoObject);
                $em->flush();

            }

            $this->get('session')->getFlashBag()->set(
                'notice',
                'Pago realizado con éxito. El saldo de los regalos ha sido entregado.'
            );
            $this->get('session')->getFlashBag()->set(
                'transaccion',
                'Vaciar Carrito, la transacción fue correcta'
            );

            // Se manda correo al comprador con la confirmación de su compra
            $message = \Swift_Message::newInstance()
            ->setSubject("Tu regalo ha sido entregado correctamente en PartyGift")
            ->setFrom("info@newlywishes.com")
            ->setTo($details["EMAIL"])
            ->setContentType("text/html")
            ->setBody(
                $this->renderView(
                    'PGPartyBundle:Default:correoRegalasteAlgo.html.twig', array(
                        'details' => $details, 
                        'usuario' => $usuarioObject,
                        'bucketgift' => $regaloObject->getBucketGift()->getTitulo(),
                    )
                )
            );
            $this->get('mailer')->send($message);

            // Se le manda un correo al usuario de que le han regalado algo de su BucketGift
            $message = \Swift_Message::newInstance()
            ->setSubject("Un invitado te ha regalado algo en PartyGift")
            ->setFrom("info@newlywishes.com")
            ->setTo($usuarioObject->getEmail())
            ->setContentType("text/html")
            ->setBody(
                $this->renderView(
                    'PGPartyBundle:Default:correoTeRegalaronAlgo.html.twig', array(
                        'details' => $details,  
                    )
                )
            );
            $this->get('mailer')->send($message);

            /* Comentar esto
            return $this->render('NWPaymentBundle:Details:view.html.twig', array(
                'status' => $status->getValue(),
                'details' => $details,
                'paymentTitle' => ucwords(str_replace(array('_', '-'), ' ', $token->getPaymentName()))
            )); // Comentar hasta aqui */

        } else if ($status->isPending()) {
            $this->get('session')->getFlashBag()->set(
                'notice',
                'Payment is still pending. Credits were not added'
            );
        } else {
            $this->get('session')->getFlashBag()->set('error', 'Payment failed');
        }

        return $this->redirect($this->generateURL('pg_party_homepage'));
    }

    public function viewOrderAction(Request $request)
    {
        $token = $this->getHttpRequestVerifier()->verify($request);

        $payment = $this->getPayum()->getPayment($token->getPaymentName());

        try {
            $payment->execute(new Sync($token));
        } catch (RequestNotSupportedException $e) {}

        $payment->execute($status = new GetHumanStatus($token));

        /** @var OrderInterface $order */
        $order = $this->getPayum()->getStorage($token->getDetails()->getClass())->findModelById(
            $token->getDetails()->getId()
        );

        return $this->render('NWPaymentBundle:Details:viewOrder.html.twig', array(
            'status' => $status->getValue(),
            'order' => array(
                'client' => array(
                    'id' => $order->getClientId(),
                    'email' => $order->getClientEmail(),
                ),
                'number' => $order->getNumber(),
                'description' => $order->getCurrencyCode(),
                'total_amount' => $order->getTotalAmount(),
                'currency_code' => $order->getCurrencyCode(),
                'currency_digits_after_decimal_point' => $order->getCurrencyDigitsAfterDecimalPoint(),
                'details' => $order->getDetails(),
            ),
            'paymentTitle' => ucwords(str_replace(array('_', '-'), ' ', $token->getPaymentName()))
        ));
    }
}