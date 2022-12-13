<?php

namespace Bakhshi\Immodvisor;

use JsonException;

class ImmoConfig
{

    /**
     * @param string $apiKey
     * @param string $saltIn
     * @param string $saltOut
     * @param int $idCompany
     * @param int $max
     * @return array[]
     * @throws JsonException
     */
    public function lastReviews(string $apiKey, string $saltIn, string $saltOut, int $idCompany, int $max): array
    {

        $feedbacks = array();
        if ($idCompany !== 0) {

            $api = new Api($apiKey,
                $saltIn,
                $saltOut);
            $api->env('prod');

            $api->debug(false);
            $reviewsData = $api->reviewList($idCompany)->parse();
            $brand_json = $api->companyGet($idCompany)->get();
            $brand = json_decode($brand_json, TRUE, 512, JSON_THROW_ON_ERROR);
            if (isset($brand["datas"]["company"]["rating"])) {
                $feedbacks = array_slice($reviewsData->datas->reviews, 0, $max);
            }
        }
        return array(
            'reviews' => $feedbacks
        );
    }

    /**
     * @param string $apiKey
     * @param string $saltIn
     * @param string $saltOut
     * @param int $idCompany
     * @return array
     * @throws JsonException
     */
    public function headerReviews(string $apiKey, string $saltIn, string $saltOut, int $idCompany): array
    {
        $infoCompnay = array();
        if ($idCompany !== 0) {
            $api = new Api($apiKey,
                $saltIn,
                $saltOut);
            $api->env('prod');
            $api->debug(false);

            $brand_json = $api->companyGet($idCompany)->get();
            $brand = json_decode($brand_json, TRUE, 512, JSON_THROW_ON_ERROR);
            if (isset($brand["datas"]["company"]["rating"])) {
                $averageRating = $brand["datas"]["company"]["rating"];
                $branchName = $brand["datas"]["company"]["address"]["city"];
                $infoCompnay = array(
                    'rate' => $averageRating,
                    'branchName' => $branchName
                );

            }
        }
        return $infoCompnay;
    }
}