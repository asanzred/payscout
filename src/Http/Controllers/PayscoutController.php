<?php

namespace Asanzred\Payscout\Http\Controllers;

use ApiSW\Http\Requests;
use ApiSW\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

use Payscout;
use \DOMDocument;
use \SimpleXMLElement;
use Config;
use Log;
use ApiSW\Models\CountryEquivalenceIso;
//use SoapClient;

/********************************************

THIS CONTROLLER IS ONLY FOR FLOW TESTING 
AND TUTORIAL PURPOSES.

FEEL FREE TO MOVE CODE TO YOUR OWN CONTROLLER
AND ROUTES TO MAKE YOUR SPECIFIC STUFF.

*********************************************/

class PayscoutController extends Controller
{

    public function stepOne(){

    $mtn        = Input::get('mtn');
    $device     = Input::get('device');
    $return_url = Input::get('return');

    $transaction = \ApiSW\Models\Transaction::where('mtn', '=', $mtn)->first();

        if($transaction)
        {
            $user    = \ApiSW\Models\User::where('client_id', '=', $transaction->client_id)->first();    

            //ultimo idioma usuario
            if($user->default_language!='')                 
                $lang = $user->default_language;
            else
            {
                $lang   = Config::get('app.available_locale');
            }

            $xmlRequest = new DOMDocument('1.0','UTF-8');

            $xmlRequest->formatOutput = true;
            $xmlSale = $xmlRequest->createElement('sale');

            if($device != "web"){
                $return_url = Config::get('payscout.redirect_url');
            }

            // Amount, authentication, and Redirect-URL are typically the bare minimum.
            Payscout::appendXmlNode($xmlRequest, $xmlSale,'api-key',Config::get('payscout.api_key'));
            Payscout::appendXmlNode($xmlRequest, $xmlSale,'redirect-url',$return_url);
            Payscout::appendXmlNode($xmlRequest, $xmlSale, 'amount', number_format($transaction->totalSale, 2,'.','')*100);
            Payscout::appendXmlNode($xmlRequest, $xmlSale, 'ip-address', \Request::ip()); //Get client IP (send with form)
            //Payscout::appendXmlNode($xmlRequest, $xmlSale, 'processor-id' , 'processor-a');
            Payscout::appendXmlNode($xmlRequest, $xmlSale, 'currency', 'USD');

            // Some additonal fields may have been previously decided by user
            Payscout::appendXmlNode($xmlRequest, $xmlSale, 'order-id', date('YmdHis').'_'.$mtn);
            Payscout::appendXmlNode($xmlRequest, $xmlSale, 'order-description', 'Small Order');
            /*Payscout::appendXmlNode($xmlRequest, $xmlSale, 'merchant-defined-field-1' , 'Red');
            Payscout::appendXmlNode($xmlRequest, $xmlSale, 'merchant-defined-field-2', 'Medium');*/
            Payscout::appendXmlNode($xmlRequest, $xmlSale, 'tax-amount' , '0.00');
            Payscout::appendXmlNode($xmlRequest, $xmlSale, 'shipping-amount' , '0.00');


            $xmlBillingAddress = $xmlRequest->createElement('billing');
            Payscout::appendXmlNode($xmlRequest, $xmlBillingAddress,'first-name', $user->name);
            Payscout::appendXmlNode($xmlRequest, $xmlBillingAddress,'last-name', $user->surname);
            Payscout::appendXmlNode($xmlRequest, $xmlBillingAddress,'address1', $user->address);
            Payscout::appendXmlNode($xmlRequest, $xmlBillingAddress,'city', $user->city);
            Payscout::appendXmlNode($xmlRequest, $xmlBillingAddress,'state', $user->state);
            Payscout::appendXmlNode($xmlRequest, $xmlBillingAddress,'postal', $user->cp);
            //billing-address-email
            $country = CountryEquivalenceIso::whereIso3($user->country)->first();
            Payscout::appendXmlNode($xmlRequest, $xmlBillingAddress,'country', $country->iso2);
            Payscout::appendXmlNode($xmlRequest, $xmlBillingAddress,'email', $user->email);
            Payscout::appendXmlNode($xmlRequest, $xmlBillingAddress,'phone', $user->phone);
            Payscout::appendXmlNode($xmlRequest, $xmlBillingAddress,'company', '');
            Payscout::appendXmlNode($xmlRequest, $xmlBillingAddress,'address2', '');
            Payscout::appendXmlNode($xmlRequest, $xmlBillingAddress,'fax', '');
            $xmlSale->appendChild($xmlBillingAddress);


            $shippingaddress1 ='';
            $shippingaddress1 ='';
            $shippingcity     ='';
            $shippingstate    ='';
            $shippingcp       ='';
            $shippingcountry  ='';

            $xmlShippingAddress = $xmlRequest->createElement('shipping');
            Payscout::appendXmlNode($xmlRequest, $xmlShippingAddress,'first-name', $user->name);
            Payscout::appendXmlNode($xmlRequest, $xmlShippingAddress,'last-name', $user->surname);
            Payscout::appendXmlNode($xmlRequest, $xmlShippingAddress,'address1', $shippingaddress1);
            Payscout::appendXmlNode($xmlRequest, $xmlShippingAddress,'city', $shippingcity);
            Payscout::appendXmlNode($xmlRequest, $xmlShippingAddress,'state', $shippingstate);
            Payscout::appendXmlNode($xmlRequest, $xmlShippingAddress,'postal', $shippingcp);
            Payscout::appendXmlNode($xmlRequest, $xmlShippingAddress,'country', $shippingcountry);
            Payscout::appendXmlNode($xmlRequest, $xmlShippingAddress,'phone', $user->phone);
            Payscout::appendXmlNode($xmlRequest, $xmlShippingAddress,'company', '');
            Payscout::appendXmlNode($xmlRequest, $xmlShippingAddress,'address2', '');
            $xmlSale->appendChild($xmlShippingAddress);


            // Products already chosen by user
            $xmlProduct = $xmlRequest->createElement('product');
            Payscout::appendXmlNode($xmlRequest, $xmlProduct,'product-code' , $mtn);
            Payscout::appendXmlNode($xmlRequest, $xmlProduct,'description' , 'test product description');
            Payscout::appendXmlNode($xmlRequest, $xmlProduct,'commodity-code' , 'abc');
            Payscout::appendXmlNode($xmlRequest, $xmlProduct,'unit-of-measure' , 'USD');
            Payscout::appendXmlNode($xmlRequest, $xmlProduct,'unit-cost' , number_format($transaction->totalSale, 2,'.',''));
            Payscout::appendXmlNode($xmlRequest, $xmlProduct,'quantity' , '1');
            Payscout::appendXmlNode($xmlRequest, $xmlProduct,'total-amount' , '0.00');
            Payscout::appendXmlNode($xmlRequest, $xmlProduct,'tax-amount' , '0.00');

            Payscout::appendXmlNode($xmlRequest, $xmlProduct,'tax-rate' , '0.00');
            Payscout::appendXmlNode($xmlRequest, $xmlProduct,'discount-amount', '0.00');
            Payscout::appendXmlNode($xmlRequest, $xmlProduct,'discount-rate' , '0.00');
            Payscout::appendXmlNode($xmlRequest, $xmlProduct,'tax-type' , 'sales');
            Payscout::appendXmlNode($xmlRequest, $xmlProduct,'alternate-tax-id' , '12345');

            $xmlSale->appendChild($xmlProduct);

            $xmlRequest->appendChild($xmlSale);

            // Process Step One: Submit all transaction details to the Payment Gateway except the customer's sensitive payment information.
            // The Payment Gateway will return a variable form-url.
            $data = Payscout::sendXMLviaCurl($xmlRequest,Config::get('payscout.gateway_url'));
            //return '<pre>'.$data.'</pre>';

            // Parse Step One's XML response
            $response = @new SimpleXMLElement($data);
            if ((string)$response->result ==1 ) {
                return response()->json([
                    'msg'           =>  '[OK]',
                    'response'       =>  $response
                    ], 200
                );
            }            
        }

        return array("notificationResponse" => false);
    }

    public function stepTwo(){
        return print_r(Input::all(),true);
    }

    public function stepThree(){
        return print_r(Input::all(),true);
    }
}