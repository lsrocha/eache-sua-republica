<?php
namespace core;

use core\Database;

/**
 * @author Leonardo Rocha <leonardo.lsrocha@gmail.com>
 * @package core
 */
class Republicas
{
    /**
     * @var object
     */
    private $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    /**
     * @param (float|int|string)[] $republica
     * @param int $who_posted
     */
    public function addRepublica(array $republica, $whoPosted) {
        $filterFloat = array(
            'filter' => FILTER_SANITIZE_NUMBER_FLOAT,
            'flags' => FILTER_FLAG_ALLOW_FRACTION
        );

        $options = array(
            'name' => FILTER_SANITIZE_STRING,
            'latitude' => $filterFloat,
            'longitude' => $filterFloat,
            'phone' => FILTER_SANITIZE_STRING,
            'email' => FILTER_SANITIZE_EMAIL,
            'address' => FILTER_SANITIZE_STRING,
            'gener' => FILTER_SANITIZE_STRING,
            'num_dwellers' => FILTER_SANITIZE_NUMBER_INT,
            'vacancy_type' => FILTER_SANITIZE_STRING,
            'price' => $filterFloat,
            'more' => FILTER_SANITIZE_STRING
        );

        $republica = filter_var_array($republica, $options);
        $whoPosted = filter_var($whoPosted, FILTER_SANITIZE_NUMBER_INT);
        
        $valid = (bool) filter_var($republica['email'], FILTER_VALIDATE_EMAIL);
        $valid &= (bool) filter_var($whoPosted, FILTER_VALIDATE_INT);

        if ($valid) {
            $this->database->connect();

            $sql = "
                INSERT INTO republicas(
                    name, latitude, longitude, 
                    phone, email, price, 
                    address, gener, vacancy_type, 
                    num_dwellers, who_posted, more
                )
                VALUES(
                    '{$republica['name']}', '{$republica['latitude']}',
                    '{$republica['longitude']}', '{$republica['phone']}',
                    '{$republica['email']}', '{$republica['price']}',
                    '{$republica['address']}', '{$republica['gener']}',
                    '{$republica['vacancy_type']}', '{$republica['num_dwellers']}',
                    '{$whoPosted}', '{$republica['more']}'
                )
            ";

            $result = $this->database->query($sql);
            $this->database->disconnect();
        }

        return $result;
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @param $radio
     */
    public function getRepublicas($latitude, $longitude, $radius)
    {
        $filterFloat = array(
            'filter' => FILTER_SANITIZE_NUMBER_FLOAT,
            'flags' => FILTER_FLAG_ALLOW_FRACTION
        );

        $latitude = filter_var($latitude, $filterFloat);
        $longitude = filter_var($longitude, $filterFloat);
        $radius = filter_var($radius, FILTER_SANITIZE_NUMBER_INT);
        
        //Haversine formula
        $sql = "
            SELECT *, (6371 * acos( 
                cos(radians({$latitude})) * cos(radians(latitude)) * 
                cos(radians(longitude) - radians({$longitude})) + 
                sin(radians({$latitude})) * sin(radians(latitude))
            )) 
            AS distance 
            FROM republicas 
            HAVING distance<{$radius} 
            ORDER BY distance 
        ";

        $this->database->connect();
        $result = $this->database->query($sql);

        $json = array();
        
        while ($item = $result->fetch_assoc()) {
            $json[] = $item;
        }
        
        $this->database->disconnect();
    
        return json_encode($json);
    }
}

