<?php
/**
 *
 * User: davidlin
 * Date: 11/09/13
 * Time: 10:25 PM
 *
 */

namespace Dlin\Geocoder\Geocoding;


use Dlin\Geocoder\GeoAddress;

class GoogleGeocoding implements IGeocoding{

    private $name;

    private $clientId;

    private $privateKey;

    /**
     * Constructor
     *
     * @param $clientId
     * @param $privateKey
     */
    public function __construct($clientId=null, $privateKey=null)
    {

        $this->clientId = $clientId;
        $this->privateKey = $privateKey;

    }


    public function setName($name){
        $this->name = $name;
    }

    public function getName(){
        return $this->name;
    }



    /**
     * This function use to parse the return components from google geocoding api
     * @param $components
     * @return GeoAddress
     */
    private function _parseComponent($components)
    {

        $address = new GeoAddress();
        $address->geoCoding = $this->name;
        $streetNumber = '';
        $routeName = '';

        foreach ($components as $component) {
            if (isset($component['types'])) {
                if (in_array('street_number', $component['types'])) {
                    $streetNumber = $component['short_name'];
                }

                if (in_array('route', $component['types'])) {
                    $routeName = $component['short_name'];
                }

                if (in_array('administrative_area_level_1', $component['types'])) {
                    $address->state = $component['short_name'];
                }

                if (in_array('locality', $component['types'])) {
                    $address->suburb = $component['short_name'];
                }

                if (in_array('postal_code', $component['types'])) {
                    $address->postcode = $component['short_name'];
                }

                if (in_array('country', $component['types'])) {
                    $address->country = $component['long_name'];
                }
            }

        }



        if ($routeName || $streetNumber) {
            $address->addressLine1 = trim($streetNumber . ' ' . $routeName);
        }



        return $address;
    }


    /**
     *
     * Forward Geocoding is the process of taking a given location in address format and returning the
     * closes known coordinates to the address provided. The address can be a country, county, city,
     * state, zip code, street address, or any combination of these.
     *
     *
     *
     * @param $address
     * @return GeoAddress
     */
    public function forward($address, $countryCode=null)
    {

        $url = "http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=";
        $url = $url . urlencode($address);

        if($countryCode){
            $url.="&components=country:$countryCode";
        }



        if ($this->clientId && $this->privateKey) {
            $url .= '&client=' . $this->clientId;
            $url = $this->_signGoogleUrl($url, $this->privateKey);
        }


        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $url);
        $resp_json = curl_exec($c);
        curl_close($c);


        $resp = json_decode($resp_json, true);


        if ($resp && $resp['status'] == 'OK') {

            //get the first/best result
            $res = array_pop($resp['results']);



            $address = $this->_parseComponent($res['address_components']);
            $address->latitude = $res['geometry']['location']['lat'];
            $address->longitude = $res['geometry']['location']['lng'];
            $address->partial  = isset($res['partial_match']) && $res['partial_match'] == 1;
            $address->formattedAddress  = $res['formatted_address'];

            return $address;
        } else {
            return null;
        }


    }



    /**
     *
     * Reverse Geocoding is the oposite of Forward Geocoding.
     * It takes the provided coordinates (latitude and longitude)
     * and provides you the closest known location to that point in address format.
     *
     * @param $lat integer degree value
     * @param $long integer degree value
     * @return GeoAddress
     */
    public function reverse($lat, $long)
    {
        $url = "http://maps.googleapis.com/maps/api/geocode/json?&sensor=false&latlng=$lat,$long";


        if ($this->clientId && $this->privateKey) {
            $url .= '&client=' . $this->clientId;
            $url = $this->_signGoogleUrl($url, $this->privateKey);
        }


        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $url);
        $resp_json = curl_exec($c);
        curl_close($c);

        $resp = json_decode($resp_json, true);



        if ($resp && $resp['status'] == 'OK') {
            $res = reset($resp['results']);

            $address = $this->_parseComponent($res['address_components']);
            $address->latitude = $res['geometry']['location']['lat'];
            $address->longitude = $res['geometry']['location']['lng'];
            $address->partial  = false;
            $address->formattedAddress  = $res['formatted_address'];

            return $address;
        } else {
            return null;
        }


    }


    /**
     *
     *  Encode a string to URL-safe base64
     * @param $value
     * @return mixed
     */
    private function _encodeBase64UrlSafe($value)
    {
        return str_replace(array('+', '/'), array('-', '_'),
            base64_encode($value));
    }

    /**
     * Decode a string from URL-safe base64
     */
    private function _decodeBase64UrlSafe($value)
    {
        return base64_decode(str_replace(array('-', '_'), array('+', '/'),
            $value));
    }

    /**
     *
     * Sign a URL with a given crypto key
     * Note that this URL must be properly URL-encoded
     */
    private function _signGoogleUrl($myUrlToSign, $privateKey)
    {
        // parse the url
        $url = parse_url($myUrlToSign);

        $urlPartToSign = $url['path'] . "?" . $url['query'];

        // Decode the private key into its binary format
        $decodedKey = $this->_decodeBase64UrlSafe($privateKey);

        // Create a signature using the private key and the URL-encoded
        // string using HMAC SHA1. This signature will be binary.
        $signature = hash_hmac("sha1", $urlPartToSign, $decodedKey, true);

        $encodedSignature = $this->_encodeBase64UrlSafe($signature);

        return $myUrlToSign . "&signature=" . $encodedSignature;
    }

}