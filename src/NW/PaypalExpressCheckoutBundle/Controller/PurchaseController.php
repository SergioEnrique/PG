<?php
namespace NW\PaypalExpressCheckoutBundle\Controller;

use NW\PaymentBundle\Model\PaymentDetails;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Paypal\ExpressCheckout\Nvp\Api;
use Payum\Core\Registry\RegistryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Extra;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\HttpFoundation\Response;

class PurchaseController extends Controller
{

    /**
     * @Extra\Route(
     *   "/carrito",
     *   name="nw_paypal_express_checkout_carrito"
     * )
     * 
     * @Extra\Template("NWPaypalExpressCheckoutBundle:Purchase:nwprepare.html.twig")
     */
    public function carritoAction()
    {
        //return new Response("Este sera el carrito");
        return array(
            'paymentName' => 'paypal_express_checkout_and_doctrine_orm'
        );
    }

    /**
     * @Extra\Route(
     *   "/prepare_purchase",
     *   name="nw_paypal_express_checkout_prepare_purchase"
     * )
     */
    public function preparePurchaseAction(Request $request)
    {
        $paymentName = 'paypal_express_checkout_and_doctrine_orm';
        
        if('POST' === $request->getMethod())
        {
            // Almacén de datos del carrito
            $storage = $this->getPayum()->getStorage('NW\PaymentBundle\Entity\PaymentDetails');

            // Se genera un nuevo modelo con detalles del pago
            /** @var $paymentDetails PaymentDetails */
            $paymentDetails = $storage->create();

            // Costo total de la compra (dos decimales sin punto) $1.25 = 125
            $totalAmount = 0;

            // Se genera cada producto a comprar
            $itemCount = $request->request->get('itemCount');

            $i=0;
            $j=1;
            while ($i < $itemCount) {
                $itemCantidad = $request->request->get('item_quantity_'.$j);
                $itemPrecio = $request->request->get('item_price_'.$j);
                $totalAmount += $itemCantidad * $itemPrecio;

                $split_options = explode(",", $request->request->get('item_options_'.$j));
                $itemDescripcion = explode(":", $split_options[0]);
                $itemId = explode(":", $split_options[1]);

                $paymentDetails['L_PAYMENTREQUEST_0_NAME'.$i] = $request->request->get('item_name_'.$j)." (parte)";
                $paymentDetails['L_PAYMENTREQUEST_0_DESC'.$i] = trim($itemDescripcion[1]);
                $paymentDetails['L_PAYMENTREQUEST_0_QTY'.$i] = $itemCantidad;
                $paymentDetails['L_PAYMENTREQUEST_0_AMT'.$i] = $itemPrecio;
                $paymentDetails['L_PAYMENTREQUEST_0_NUMBER'.$i] = trim($itemId[1]);

                $j++;
                $i++;
            }

            // Datos de la compra
            $paymentDetails['PAYMENTREQUEST_0_CURRENCYCODE'] = $request->request->get('currency');
            $paymentDetails['PAYMENTREQUEST_0_CUSTOM'] = $itemCount;
            $paymentDetails['PAYMENTREQUEST_0_ITEMAMT'] = $totalAmount;
            $paymentDetails['PAYMENTREQUEST_0_HANDLINGAMT'] = ($totalAmount * .06) + 4;
            $totalAmount += ($totalAmount * .06) + 4;
            $paymentDetails['PAYMENTREQUEST_0_AMT'] = $totalAmount;
            $paymentDetails['LOCALECODE'] = 'en_US';
            
            

            $storage->update($paymentDetails);

            $captureToken = $this->getTokenFactory()->createCaptureToken(
                $paymentName,
                $paymentDetails,
                'nw_payment_details_view'
            );

            $paymentDetails['RETURNURL'] = $captureToken->getTargetUrl();
            $paymentDetails['CANCELURL'] = $captureToken->getTargetUrl();
            $paymentDetails['INVNUM'] = $paymentDetails->getId();
            $storage->update($paymentDetails);

            return $this->redirect($captureToken->getTargetUrl());
        }

        return new Response ("Error al obtener carrito");
    }

    /**
     * @return RegistryInterface
     */
    protected function getPayum()
    {
        return $this->get('payum');
    }

    /**
     * @return GenericTokenFactoryInterface
     */
    protected function getTokenFactory()
    {
        return $this->get('payum.security.token_factory');
    }

}