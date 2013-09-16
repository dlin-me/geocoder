<?php
/**
 * 
 * User: davidlin
 * Date: 13/09/13
 * Time: 12:09 AM
 * 
 */

namespace Dlin\Geocoder\Geocoding;


interface IGeocoding {
    function forward($address);
    function reverse($lat, $long);
    function setName($name);
    function getName();
}