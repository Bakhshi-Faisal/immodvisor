<?php
namespace Bakhshi\Immodvisor;

class ImmoConfig
{
    public function lastReviews($apiKey,$saltIn,$saltOut,$idCompany,$max){
        $api = new Api($apiKey,
            $saltIn,
            $saltOut);
        $api->env('prod');

        $api->debug(false);
        $reviewsData = $api->reviewList($idCompany ?? '')->parse();
        $brand_json = $api->companyGet($idCompany ?? '')->get();
        $brand = json_decode($brand_json, TRUE);
        $averageRating = $brand["datas"]["company"]["rating"];
        $averageRating = str_replace(".", ",", $averageRating);

        return array(
            'reviews' => array_slice($reviewsData->datas->reviews,0,$max)
        );
    }
    public function headerReviews($apiKey,$saltIn,$saltOut,$idCompany){
        $api = new Api($apiKey,
            $saltIn,
            $saltOut);
        $api->env('prod');
        $api->debug(false);
        $reviewsData = $api->reviewList($idCompany ?? '')->parse();
        $brand_json = $api->companyGet($idCompany ?? '')->get();
        $brand = json_decode($brand_json, TRUE);
        $averageRating = $brand["datas"]["company"]["rating"];
        
        return $averageRating;
        
    }
}