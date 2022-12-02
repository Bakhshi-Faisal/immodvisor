<?php
namespace Bakhshi\Immodvisor;

class ImmoConfig
{
    public function lastReviews($apiKey,$saltIn,$saltOut,$idCompany,$max){

        $feedbacks = array();
        $api = new Api($apiKey,
            $saltIn,
            $saltOut);
        $api->env('prod');
        $infoSociete = $api->companyGet();

        $api->debug(false);
        $reviewsData = $api->reviewList($idCompany)->parse();
        $brand_json = $api->companyGet($idCompany)->get();
        $brand = json_decode($brand_json, TRUE, 512, JSON_THROW_ON_ERROR);
        if (isset($brand["datas"]["company"]["rating"])){
            $averageRating =isset($brand["datas"]["company"]["rating"]);
            $averageRating = str_replace(".", ",", $averageRating);
            $feedbacks = array_slice($reviewsData->datas->reviews,0,$max);
        }

        return array(
            'reviews' => $feedbacks
        );
    }
    public function headerReviews($apiKey,$saltIn,$saltOut,$idCompany){

        $api = new Api($apiKey,
            $saltIn,
            $saltOut);
        $api->env('prod');
        $api->debug(false);
        $infoCompnay = array();
        $reviewsData = $api->reviewList($idCompany ?? '')->parse();
        $brand_json = $api->companyGet($idCompany ?? '')->get();
        $brand = json_decode($brand_json, TRUE, 512, JSON_THROW_ON_ERROR);
        if (isset($brand["datas"]["company"]["rating"])) {
            $averageRating = $brand["datas"]["company"]["rating"];
            $branchName = $brand["datas"]["company"]["address"]["city"];
            $infoCompnay = array(
                'rate' => $averageRating,
                'branchName' => $branchName
            );

        }
        return $infoCompnay;

    }
}