<?php

namespace Bakhshi\Immodvisor;

/**
 * Méthodes publiques de la classe Api
 */
interface iApi
{
    /**
     * Initialisation et paramétrage de l'api
     */
    public function __construct($api_key, $checksum_salt_in, $checksum_salt_out);

    public function setFormat($format);

    public function setReferer($referer);

    public function debug($bool);

    /**
     * Informations liées au dernier service appelé
     */
    public function getOutputFormat();

    public function getService();

    public function getContent();

    public function get();

    public function getHttpCode();

    public function getUrl();

    public function getError();

    /**
     * Services disponibles
     */
    public function test();

    public function config();

    public function mapDepartments();

    public function activities();

    public function companyGet($id = null, $custom_id = null, $nbr_reviews = 0);

    public function companyList($nbr_reviews = 0, bool $enable = true);

    public function companyRichSnippets();

    public function companyCreate($datas = array());

    public function companyUpdate($id = null, $custom_id = null, $datas = array());

    public function proGet($id = null, $custom_id = null);

    public function proList($company_id = null, $company_custom_id = null, bool $enable = true);

    public function proCreate($company_id = null, $company_custom_id = null, $datas = array());

    public function proUpdate($id = null, $custom_id = null, $datas = array());

    public function proLink($id = null, $custom_id = null, $company_id = null, $company_custom_id = null);

    public function proUnlink($id = null, $custom_id = null, $company_id = null, $company_custom_id = null);

    public function reviewList($company_id = null, $company_custom_id = null, $date_start = null, $date_stop = null);

    public function reviewCollect($company_id = null, $company_custom_id = null, $email = null, $mobile = null, $firstname = null, $lastname = null, $pro_id = null, $pro_custom_id = null, $activity_id = null, $highlight_number = null, $custom_ref = null);

    public function reviewCollectMultiple($datas = array());

    public function teamGet($id = null);

    public function teamList($company_id = null, $company_custom_id = null);

    public function teamCreate($company_id = null, $company_custom_id = null, $name = null);

    public function teamUpdate($id = null, $name = null);

    public function teamDelete($id);

    public function teamProLink($id = null, $custom_id = null, $pro_id = null, $pro_custom_id = null);

    public function teamProUnlink($id = null, $custom_id = null, $pro_id = null, $pro_custom_id = null);

    public function googleCollect($company_id = null, $company_custom_id = null, $email = null, $mobile = null, $type = null, $sms_content = null, $sending_date = null);

    public function googleCollectMultiple($datas = array());

    public function folderCreate($company_id = null, $company_custom_id = null, $name = null, $reference = null, $status = null, $start_date = null, $end_date = null);

    public function folderUpdate($id = null, $name = null, $reference = null, $status = null, $start_date = null, $end_date = null);

    public function folderDelete($id = null);

    public function folderList($company_id = null, $company_custom_id = null);

    public function folderGet($id = null);

    /**
     * Analyse des résultats
     */
    public function parse();

    public function check();
}