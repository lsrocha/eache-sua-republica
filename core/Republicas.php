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
     * @param (int|string)[] $republica
     * @param int $who_posted
     */
    public function addRepublica(array $republica, $who_posted) {
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
            
            $sql = <<<EOT
INSERT INTO republicas(
    name, latitude, longitude, 
    phone, email, price, 
    address, gener, vacancy_type, 
    num_dwellers, who_posted, more
)
VALUE(
    '{$republica['name']}', '{$republica['latitude']}', '{$republica['longitude']}',
    '{$republica['phone']}', '{$republica['email']}', '{$republica['price']}',
    '{$republica['address']}', '{$republica['gener']}', '{$republica['vacancy_type']}',
    '{$republica['num_dwellers']}', '{$who_posted}', '{$republica['more']}'
)
EOT;

            return $this->database->basicQuery($sql);
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

