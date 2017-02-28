<?php namespace Asanzred\Payscout;

use Config;
class Payscout
{
    /**
     * Create a new Skeleton Instance
     */
    public function __construct()
    {
        // constructor body
    }

    public function generateTransaction(){

    }


    public static function appendXmlNode($domDocument, $parentNode, $name, $value) {
            $childNode      = $domDocument->createElement($name);
            $childNodeValue = $domDocument->createTextNode($value);
            $childNode->appendChild($childNodeValue);
            $parentNode->appendChild($childNode);
    }


    public static function sendXMLviaCurl($xmlRequest,$gatewayURL) {
       // helper function demonstrating how to send the xml with curl


        $ch = curl_init(); // Initialize curl handle
        curl_setopt($ch, CURLOPT_URL, $gatewayURL); // Set POST URL

        $headers = array();
        $headers[] = "Content-type: text/xml";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Add http headers to let it know we're sending XML
        $xmlString = $xmlRequest->saveXML();
        curl_setopt($ch, CURLOPT_FAILONERROR, 1); // Fail on errors
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Allow redirects
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return into a variable
        curl_setopt($ch, CURLOPT_PORT, 443); // Set the port number
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Times out after 30s
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlString); // Add XML directly in POST

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);


        // This should be unset in production use. With it on, it forces the ssl cert to be valid
        // before sending info.
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        if (!($data = curl_exec($ch))) {
            print  "curl error =>" .curl_error($ch) ."\n";
            throw New Exception(" CURL ERROR :" . curl_error($ch));

        }
        curl_close($ch);

        return $data;
    }

    /**
     * Friendly welcome
     *
     * @param string $phrase Phrase to return
     *
     * @return string Returns the phrase passed in
     */
    public function echoPhrase($phrase)
    {
        return $phrase;
    }

    public static function printField($name, $value = "") {
        return "<input type=hidden name=\"$name\" value=\"$value\">\n";
    }

    public static function printHash($fields) {
        $gw_merchantKeyText = Config::get('payscout.api_key');
        $stringToHash = implode('|', array_values($fields)) .
            "|" . $gw_merchantKeyText;
        $value = implode("|", array_keys($fields)) . "|" . md5($stringToHash);
        
        return $value;
    }
}
