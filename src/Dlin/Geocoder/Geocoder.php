<?php
/**
 *
 * User: davidlin
 * Date: 10/09/13
 * Time: 11:40 PM
 *
 */

namespace Dlin\Geocoder;
use Dlin\Geocoder\Geocoding\BingGeocoding;
use Dlin\Geocoder\Geocoding\GoogleGeocoding;

/**
 *
 *
 * Class Geocoder
 *
 * This class handles the rotating, weighting of multiple geocoding service provider
 *
 * @package Dlin\Geocoder
 *
 */
class Geocoder
{

    //configuration
    public $sourceConfig;

    /**
     * Constructor
     *
     * @param $sourceConfig This can be an array of configuration of a path to a configuration .ini file
     */
    public function __construct($sourceConfig)
    {

        if (is_string($sourceConfig) && is_file($sourceConfig)) {
            $this->sourceConfig = parse_ini_file($sourceConfig, true);
        } else if (is_array($sourceConfig)) {
            $this->sourceConfig = $sourceConfig;
        } else {
            throw new \Exception('Invalid configuration parameter');
        }

        //assign default weight
        foreach($this->sourceConfig as $name=>&$config){
            if(!isset($config['weight'])){
                $config['weight'] = 1;
            }
        }

        uasort($this->sourceConfig, function($a, $b){$aw =isset($a['weight'])?$a['weight']:0;$bw = isset($b['weight'])?$b['weight']:0;return $bw-$aw;});

    }

    private $_callCounter = 0;

    /**
     * @return BingGeocoding|GoogleGeocoding
     * @throws \Exception
     */
    private function getGeocoding(){

        $keys = array_keys($this->sourceConfig);

        if(count($keys) == 0 ){
            throw new \Exception('No Geocoding source defined');
        }

        $selectedIndex = $this->_callCounter%count($keys);

        $configName = $keys[$selectedIndex];

        $config = $this->sourceConfig[$configName];



        $geocoding = null;
        switch(strtolower($config['vendor'])){
            case 'bing':
                if(!isset($config['key'])){
                    throw new \Exception('Missing key configuration in :'.$configName);
                }
                $geocoding = new BingGeocoding($config['key']);
                break;

            case 'google':
                $client = isset($config['client']) ?$config['client'] : null;
                $key = isset($config['key']) ?$config['key'] : null;
                $geocoding = new GoogleGeocoding($client, $key);
                break;
            default:
                throw new \Exception('Invalid vendor configuration:'.$config['vendor']);

        }

        $this->_callCounter++;



        $geocoding->setName($configName);

        return $geocoding;


    }

    /**
     * Forward Geocoding is the process of taking a given location in address format and returning the
     * closes known coordinates to the address provided. The address can be a country, county, city,
     * state, zip code, street address, or any combination of these.
     *
     * @param $address
     * @return GeoAddress
     */
    public function forward($address){

        $attempt = 0;
        do{
            $coding = $this->getGeocoding();
            $attempt++;
        }while($coding !== null & $attempt <= count($this->sourceConfig));

        if($coding===null){
            throw new \Exception('No valid Geocoding provider found');
        }


        return $coding->forward($address);

    }


    /**
     *
     * Reverse Geocoding is the opposite of Forward Geocoding.
     * It takes the provided coordinates (latitude and longitude)
     * and provides you the closest known location to that point in address format.
     *
     * @param $lat integer degree value
     * @param $long integer degree value
     * @return GeoAddress
     */
    public function reverse($lat, $long)
    {
        $attempt = 0;
        do{
            $coding = $this->getGeocoding();
            $attempt++;
        }while($coding !== null & $attempt <= count($this->sourceConfig));

        if($coding===null){
            throw new \Exception('No valid Geocoding provider found');
        }

        return $coding->reverse($lat, $long);
    }



}
