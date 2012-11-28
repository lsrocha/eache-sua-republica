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
    public function addRepublica(array $republica, $who_posted) {
        $filterFloat = array(
            'filter' => FILTER_SANITIZE_NUMBER_FLOAT,
            'flags' => FILTER_FLAG_ALLOW_FRACTION
        );

        $filterString = array(
            'filter' => FILTER_SANITIZE_STRING,
            'flags' => FILTER_FLAG_ENCODE_HIGH
        );
            
        $options = array(
            'name' => $filterString,
            'latitude' => $filterFloat,
            'longitude' => $filterFloat,
            'phone' => FILTER_SANITIZE_STRING,
            'email' => FILTER_SANITIZE_EMAIL,
            'address' => $filterString,
            'gener' => FILTER_SANITIZE_STRING,
            'num_dwellers' => FILTER_SANITIZE_NUMBER_INT,
            'vacancy_type' => $filterString,
            'price' => $filterFloat,
            'more' => $filterString
        );

        $republica = filter_var_array($republica, $options);

        $options = array(
            'email' => FILTER_VALIDATE_EMAIL,
            'latitude' => FILTER_VALIDATE_FLOAT,
            'longitude' => FILTER_VALIDATE_FLOAT,
            'num_dwellers' => FILTER_VALIDATE_INT,
            'price' => FILTER_VALIDATE_FLOAT
        );

        $valid = (bool) filter_var_array($republica, $options);
        $valid &= (bool) filter_var($who_posted, FILTER_VALIDATE_INT);

        if ($valid) {
            /*
             * Javascript currency mask is returning an integer.
             * 2 decimal places are requested.
             */
            $republica['price'] = $republica['price']/100;

            $sql = "SELECT id FROM republicas WHERE latitude='{$republica['latitude']}' AND longitude='{$republica['longitude']}'";

            $this->database->connect();
            $result = $this->database->query($sql);

            if ($result->num_rows == 0) {
                $sql = <<<EOT
INSERT INTO republicas(
    name, latitude, longitude, 
    phone, email, price, 
    address, gener, vacancy_type, 
    num_dwellers, who_posted, more
)
VALUES(
    '{$republica['name']}', '{$republica['latitude']}', '{$republica['longitude']}',
    '{$republica['phone']}', '{$republica['email']}', '{$republica['price']}',
    '{$republica['address']}', '{$republica['gener']}', '{$republica['vacancy_type']}',
    '{$republica['num_dwellers']}', '{$who_posted}', '{$republica['more']}'
)
EOT;

                $result = $this->database->query($sql);
                $this->database->disconnect();
                
                return $result;
            }

            $this->database->disconnect();
        }

        return false;
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @param $radio
     */
    public function getRepublicas($latitude, $longitude, $radius, $num)
    {
        //Haversine formula (Google Maps API)
        $sql = <<<EOT
SELECT *, (6371 * acos( 
    cos(radians({$latitude})) * cos(radians(latitude)) * 
    cos(radians(longitude) - radians({$longitude})) + 
    sin(radians({$latitude})) * sin(radians(latitude))
)) 
AS distance 
FROM republicas 
HAVING distance<{$radius} 
ORDER BY distance 
LIMIT 0,{$num}
EOT;

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

