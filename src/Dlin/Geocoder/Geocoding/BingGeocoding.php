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

class BingGeocoding implements IGeocoding {

    private $name;
    private $key;

    /**
     * Constructor
     *
     * @param $key
     */
    public function __construct($key)
    {
        $this->key = $key;

    }


    public function setName($name){
        $this->name = $name;
    }

    public function getName(){
        return $this->name;
    }

    /**
     * This function use to parse the return components from google bing api
     * @param $components
     * @return GeoAddress
     */
    private function _parseComponent($components)
    {

        $address = new GeoAddress();
        $address->geoCoding = $this->name;
        $address->addressLine1 = $components['address']['addressLine'];
        $address->state = $components['address']['adminDistrict'];
        $address->country = $components['address']['countryRegion'];
        $address->formattedAddress = $components['address']['formattedAddress'].', '.$address->country;
        $address->suburb = $components['address']['locality'];
        $address->postcode = $components['address']['postalCode'];
        $address->partial = $components['confidence'] != 'High';
        $address->latitude = strval($components['point']['coordinates'][0]);
        $address->longitude = strval($components['point']['coordinates'][1]);

        return $address;
    }


    /**
     *
     * Forward Geocoding is the process of taking a given location in address format and returning the
     * closes known coordinates to the address provided. The address can be a country, county, city,
     * state, zip code, street address, or any combination of these.
     *
     * forwardByGoogle
     *
     * @param $address
     * @return GeoAddress
     */
    public function forward($address)
    {


        $url = "http://dev.virtualearth.net/REST/v1/Locations?includeNeighborhood=0&include=0&maxResults=1&key={$this->key}&q=";
        $url = $url.urlencode($address);


        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $url);
        $resp_json = curl_exec($c);
        curl_close($c);


        $resp = json_decode($resp_json, true);


        if($resp && $resp['statusDescription']=='OK'){

            $res = reset($resp['resourceSets']);
            if($res['estimatedTotal'] == 0){
                return null;
            }
            $res = reset($res['resources']);


            $address = $this->_parseComponent($res);

            return $address;


        }else{
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
        $url = "http://dev.virtualearth.net/REST/v1/Locations/$lat,$long?includeNeighborhood=0&key={$this->key}";
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $url);
        $resp_json = curl_exec($c);
        curl_close($c);

        $resp = json_decode($resp_json, true);

        if($resp && $resp['statusDescription']=='OK'){

            $res = reset($resp['resourceSets']);
            if($res['estimatedTotal'] == 0){
                return null;
            }
            $res = reset($res['resources']);

            $address = $this->_parseComponent($res);

            return $address;


        }else{
            return null;
        }

    }



}