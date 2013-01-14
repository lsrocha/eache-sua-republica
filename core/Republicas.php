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
     * @param (float|int|string)[] $republica
     * @param int $who_posted
     */
    public static function addRepublica(array $republica, $whoPosted, Database &$database) {
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
            'num_vacancies' => FILTER_SANITIZE_NUMBER_INT,
            'vacancy_type' => FILTER_SANITIZE_STRING,
            'price' => $filterFloat,
            'more' => FILTER_SANITIZE_STRING
        );

        $republica = filter_var_array($republica, $options);
        $whoPosted = filter_var($whoPosted, FILTER_SANITIZE_NUMBER_INT);

        $valid = (bool) filter_var($republica['email'], FILTER_VALIDATE_EMAIL);
        $valid &= (bool) filter_var($whoPosted, FILTER_VALIDATE_INT);

        $success = false;

        if ($valid) {
            $query = $database->prepare('
                INSERT INTO republicas (
                    name, latitude, longitude, phone, email, price,
                    address, gener, vacancy_type, num_dwellers, num_vacancies, 
                    who_posted, more 
                ) VALUES (
                    :name, :latitude, :longitude, :phone, :email, :price,
                    :address, :gener, :vacancy_type, :num_dwellers, 
                    :num_vacancies, :who_posted, :more
                )
            ');

            do {
                $query->bindParam(':'.key($republica), current($republica));
            } while (next($republica) !== false);

            $query->bindParam(':who_posted', $whoPosted, Database::PARAM_INT);
            $success = $query->execute();
        }

        return $success;
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @param $radio
     */
    public static function getRepublicas($latitude, $longitude, $radius, Database &$database)
    {
        $latitude = filter_var(
            $latitude,
            FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION
        );
        $longitude = filter_var(
            $longitude,
            FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION
        );
        $radius = filter_var(
            $radius,
            FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION
        );

        //Haversine formula
        $query = $database->prepare('
            SELECT *, (6371 * acos(
                cos(radians( :latitude )) * cos(radians(latitude)) *
                cos(radians(longitude) - radians( :longitude )) + 
                sin(radians( :latitude )) * sin(radians(latitude))
            ))
            AS distance
            FROM republicas
            HAVING distance < :radius
            ORDER BY distance
        ');

        $query->bindParam(':latitude', $latitude);
        $query->bindParam(':longitude', $longitude);
        $query->bindParam(':radius', $radius);

        $query->execute();

        $json = array();

        while ($item = $query->fetch(Database::FETCH_ASSOC)) {
            $json[] = $item;
        }

        return json_encode($json);
    }
}

