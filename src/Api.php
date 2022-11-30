<?php
/**
 * Classe permettant d'accéder à l'API immodvisor
 * Afin de profiter pleinement des mises à jours de cette librarie, il est fortement recommandé de ne pas la modifier
 * @author Jeremy Humbert <jeremy@immodvisor.com>
 * @copyright 2019 immodvisor
 */
namespace Bakhshi\Immodvisor;

/**
 * Méthodes publiques de la classe Api
 */
interface iApi {
    /**
     * Initialisation et paramétrage de l'api
     */
    public function __construct($api_key, $checksum_salt_in, $checksum_salt_out);
    public function setFormat($format);
    public function setReferer($host);
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
    public function companyGet($id=null, $custom_id=null, $nbr_reviews=0);
    public function companyList($nbr_reviews=0, $enable=true);
    public function companyRichSnippets();
    public function companyCreate($datas=array());
    public function companyUpdate($id=null, $custom_id=null, $datas=array());
    public function proGet($id=null, $custom_id=null);
    public function proList($company_id=null, $company_custom_id=null, $enable=true);
    public function proCreate($company_id=null, $company_custom_id=null, $datas=array());
    public function proUpdate($id=null, $custom_id=null, $datas=array());
    public function proLink($id=null, $custom_id=null, $company_id=null, $company_custom_id=null);
    public function proUnlink($id=null, $custom_id=null, $company_id=null, $company_custom_id=null);
    public function reviewList($company_id=null, $company_custom_id=null, $date_start=null, $date_stop=null);
    public function reviewCollect($company_id=null, $company_custom_id=null, $email=null, $mobile=null, $firstname=null, $lastname=null, $pro_id=null, $activity_id=null, $highlight_number=null, $custom_ref=null, $folder_id=null);
    public function reviewCollectMultiple($datas=array());
    public function teamGet($id=null);
    public function teamList($company_id=null, $company_custom_id=null);
    public function teamCreate($company_id=null, $company_custom_id=null, $name=null);
    public function teamUpdate($id=null, $name=null);
    public function teamDelete($id);
    public function teamProLink($id=null, $custom_id=null, $pro_id=null, $pro_custom_id=null);
    public function teamProUnlink($id=null, $custom_id=null, $pro_id=null, $pro_custom_id=null);
    public function googleCollect($company_id=null, $company_custom_id=null, $email=null, $mobile=null, $type=null, $params=null, $sending_date=null);
    public function googleCollectMultiple($datas=array());
    public function folderCreate($company_id=null, $company_custom_id=null, $name=null, $reference=null, $status=null, $start_date=null, $end_date=null);
    public function folderUpdate($id=null, $name=null, $reference=null, $status=null, $start_date=null, $end_date=null);
    public function folderDelete($id=null);
    public function folderList($company_id=null, $company_custom_id=null);
    public function folderGet($id=null);
    /**
     * Analyse des résultats
     */
    public function parse();
    public function check();
}

/**
 * Classe object Api
 */
class Api extends immodvisor implements iApi {

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    const STATUS_ERROR = 0;
    const STATUS_DONE = 1;
    const STATUS_MAINTENANCE = 2;

    const OUTPUT_FORMAT_JSON = 'json';
    const OUTPUT_FORMAT_XML = 'xml';

    /**
     * Paramètres
     * @access private
     * @property {string} $api_key					Clé API liée au domaine appelant
     * @property {string} $checksum_salt_in     	Grain de sel du checksum entrant côté serveur
     * @property {string} $checksum_salt_out    	Grain de sel du checksum sortant côté serveur
     * @property {string} $output_format        	Format du retour des services (json ou xml), json par défaut
     * @property {string} $referer        			Url appelante (protocol + host)
     */
    private $api_key;
    private $checksum_salt_in;
    private $checksum_salt_out;
    private $output_format;
    private $referer;

    /**
     * Propriétés
     * @access protected
     * @property {string} $service					Nom du dernier service appelé
     * @property {string} $content     				Contenu du dernier service appelé
     * @property {int} $http_code    				Code http du dernier service appelé
     * @property {string} $url_real        			Url du dernier service appelé
     * @property {string} $error        			Erreur du dernier service appelé (renseigné lors de l'appel de la méthode check())
     * @property {string} $ch_error        			Erreur du dernier appel curl
     * @property {bool} $debug        				Mode debug activé ou non (valable uniquement en environnement dev)
     */
    protected $service;
    protected $content;
    protected $http_code;
    protected $url_real;
    protected $error;
    protected $ch_error;
    protected $debug;

    /**
     * Constructeur
     * @access public
     * @param {string} $api_key						Clé API liée au domaine appelant
     * @param {string} $checksum_salt_in     		Grain de sel du checksum entrant côté serveur
     * @param {string} $checksum_salt_out    		Grain de sel du checksum sortant côté serveur
     * @return {object} 							Objet de la classe Api
     */
    public function __construct($api_key, $checksum_salt_in, $checksum_salt_out) {
        if(is_string($api_key) && preg_match('`^[0-9a-zA-Z-]+$`', $api_key)) {
            $this->api_key = $api_key;
        }
        if(is_string($checksum_salt_in) && !empty($checksum_salt_in)) {
            $this->checksum_salt_in = $checksum_salt_in;
        }
        if(is_string($checksum_salt_out) && !empty($checksum_salt_out)) {
            $this->checksum_salt_out = $checksum_salt_out;
        }
        $this->output_format = self::OUTPUT_FORMAT_JSON;
        $this->debug = false;
    }

    /**
     * Initialisation d'un service
     * @access private
     * @param {string} $service						Nom du service de type constructeur/action
     */
    protected function init($service) {
        $this->content = null;
        $this->http_code = null;
        $this->url_real = null;
        $this->error = null;
        $this->ch_error = null;
        $this->service = $service;
    }

    /**
     * Modification du format de retour des services
     * @access public
     * @param {string} $format						Format du retour des services (json ou xml)
     * @return {object|false} 						False en cas d'échec, objet courant sinon
     */
    public function setFormat($format) {
        if(is_string($format) && in_array($format, array(self::OUTPUT_FORMAT_JSON, self::OUTPUT_FORMAT_XML))) {
            $this->output_format = $format;
            return $this;
        }
        return false;
    }

    /**
     * Modification du referer appelant, autorisé à appelé les services
     * @access public
     * @param {string} $referer						Referer (Ex : http://test.example.com)
     * @return {object|false} 						False en cas d'échec, objet courant sinon
     */
    public function setReferer($referer) {
        if(is_string($referer) && !empty($referer) && Utils::isUrl($referer)) {
            $this->referer = $referer;
            return $this;
        }
        return false;
    }

    /**
     * Activation/désactivation du mode debug. Le mode debug permet d'avoir des messages plus précis des services appelés
     * @access public
     * @param {bool} $bool							Boolean permettant d'activer ou non le mode debug
     * @return {object} 							Objet courant
     */
    public function debug($bool) {
        $this->debug = ($bool === true || $bool == 1) ? true : false;
        return $this;
    }

    /**
     * Retourne le format de retour des services
     * @access public
     * @return {string|null} 						Format du retour des services (json ou xml)
     */
    public function getOutputFormat() {
        return $this->output_format;
    }

    /**
     * Retourne le nom du dernier service appelé
     * @access public
     * @return {string|null} 						Nom du dernier service appelé, null si aucun appel effectué
     */
    public function getService() {
        return $this->service;
    }

    /**
     * Retourne le contenu brut du dernier service appelé
     * @access public
     * @return {string|null} 						Contenu brut du dernier service appelé, null si aucun appel effectué
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Alias de la méthode getContent()
     */
    public function get() {
        return $this->getContent();
    }

    /**
     * Retourne le code http du dernier service appelé
     * @access public
     * @return {string|null} 						Code http du dernier service appelé, null si aucun appel effectué
     */
    public function getHttpCode() {
        return $this->http_code;
    }

    /**
     * Retourne l'url complète du dernier service appelé
     * @access public
     * @return {string|null} 						Url complète du dernier service appelé, null si aucun appel effectué
     */
    public function getUrl() {
        return $this->url_real;
    }

    /**
     * Retourne l'erreur du dernier service appelé (erreur renseignée lors de l'appel à la méthode check())
     * @access public
     * @return {string|null} 						Erreur du dernier service appelé, null si aucun appel effectué ou aucune erreur
     */
    public function getError() {
        return $this->error;
    }

    /**
     * Appel du service test : teste si l'API est en service
     * @access public
     * @return {object} 							Objet courant
     */
    public function test() {
        $this->init('test');
        return $this->run(self::METHOD_GET);
    }

    /**
     * Appel du service config : récupére la configuration du serveur appelant sur le serveur appelé
     * @access public
     * @return {object} 							Objet courant
     */
    public function config() {
        $this->init('config');
        return $this->run(self::METHOD_GET);
    }

    /**
     * Appel du service map/departments : récupére la liste des départements et de leurs codes
     * @access public
     * @return {object} 							Objet courant
     */
    public function mapDepartments() {
        $this->init('map/departments');
        return $this->run(self::METHOD_GET);
    }

    /**
     * Appel du service company/activities : récupére la liste des noms des métiers
     * @access public
     * @return {object} 							Objet courant
     */
    public function activities() {
        $this->init('company/activities');
        return $this->run(self::METHOD_GET);
    }

    /**
     * Appel du service company/get : récupére les informations d'une société
     * Si aucun paramètre n'est renseigné, la société liée à la clé API est retournée. Sinon la société n'est retournée que si elle appartient elle même à la société liée à la clé API
     * @access public
     * @param {int|null} $id						Identifiant immodvisor de la société
     * @param {int|string|null} $custom_id			Identifiant client de la société
     * @param {int} $nbr_reviews					Nombre d'avis (-1 : tous les avis, 0 : aucun avis, [int] : nombre de derniers avis
     * @return {object} 							Objet courant
     */
    public function companyGet($id=null, $custom_id=null, $nbr_reviews=0) {
        $this->init('company/get');
        $datas = array();
        self::datasAddInt('id', $id, $datas);
        self::datasAddString('custom_id', $custom_id, $datas);
        self::datasAddInt('nbr_reviews', $nbr_reviews, $datas, false);
        return $this->run(self::METHOD_GET, $datas);
    }

    /**
     * Appel du service company/list : récupére les informations de toutes les sociétés liées à la clé API
     * @access public
     * @param {int} $nbr_reviews					Nombre d'avis (capé à 20 maximum)
     * @param {bool} $enable						Si true ne retourne que les sociétés actives, si false retourne toutes les sociétés
     * @return {object} 							Objet courant
     */
    public function companyList($nbr_reviews=0, $enable=true) {
        $this->init('company/list');
        $datas = array();
        self::datasAddInt('nbr_reviews', $nbr_reviews, $datas);
        self::datasAddBool('enable', $enable, $datas);
        return $this->run(self::METHOD_GET, $datas);
    }

    /**
     * Appel du service company/richsnippets : récupére les rich snippets de toutes les sociétés liées à la clé API
     * @access public
     * @param {bool} $enable						Si true ne retourne que les sociétés actives, si false retourne toutes les sociétés
     * @return {object} 							Objet courant
     */
    public function companyRichSnippets() {
        $this->init('company/richsnippets');
        return $this->run(self::METHOD_GET);
    }

    /**
     * Appel du service company/create : crée une société appartenant à la marque
     * Seules les marques peuvent utiliser ce service
     * @access public
     * @param {array} $datas						Données de la société. Dans l'ordre : custom_id, name (obligatoire), phone, fax, email, website, address, address2, zipcode, city (obligatoire), department (obligatoire), content, color, facebook_page, twitter_page
     * @return {object} 							Objet courant
     */
    public function companyCreate($datas=array()) {
        $this->init('company/create');
        if(is_object($datas)) {
            $datas = (array) $datas;
        }
        if(!is_array($datas)) {
            $datas = array();
        }
        if(!array_key_exists('name', $datas) || empty($datas['name'])) {
            $this->content = false;
            return $this;
        }
        if(array_key_exists('activities', $datas) && is_array($datas['activities'])) {
            $activities = array();
            foreach($datas['activities'] as $k => $v) {
                if(!Utils::isInt($v)) {
                    $this->content = false;
                    return $this;
                }
                $activities[] = (string) $v;
            }
            $datas['activities'] = $activities;
        }
        if(!array_key_exists('city', $datas) || empty($datas['city'])) {
            $this->content = false;
            return $this;
        }
        if(!array_key_exists('department', $datas) || empty($datas['department'])) {
            $this->content = false;
            return $this;
        }
        return $this->run(self::METHOD_POST, $datas);
    }

    /**
     * Appel du service company/update : mise à jour d'une société appartenant à la marque
     * Si aucun identifiant n'est fournit, la société liée à la clé API sera mise à jour
     * @access public
     * @param {int|null} $id						Identifiant immodvisor de la société
     * @param {int|string|null} $custom_id			Identifiant client de la société
     * @param {array} $datas						Données à mettre à jour. Dans l'ordre : custom_id, name, enable, phone, fax, email, website, address, address2, zipcode, city, department, content, color, facebook_page, twitter_page
     * @return {object} 							Objet courant
     */
    public function companyUpdate($id=null, $custom_id=null, $datas=array()) {
        $this->init('company/update');
        if(is_object($datas)) {
            $datas = (array) $datas;
        }
        if(!is_array($datas)) {
            $datas = array();
        }
        if(array_key_exists('activities', $datas) && is_array($datas['activities'])) {
            $activities = array();
            foreach($datas['activities'] as $k => $v) {
                if(!Utils::isInt($v)) {
                    $this->content = false;
                    return $this;
                }
                $activities[] = (string) $v;
            }
            $datas['activities'] = $activities;
        }
        $temp = array();
        self::datasAddInt('id', $id, $temp);
        self::datasAddString('cid', $custom_id, $temp);
        $datas = array_merge($temp, $datas);
        return $this->run(self::METHOD_PUT, $datas);
    }

    /**
     * Appel du service pro/get : récupére les informations d'un compte pro lié à au moins une des sociétés du parc
     * @access public
     * @param {int} $id								Identifiant immodvisor du pro (obligatoire)
     * @param {int|string|null} $custom_id			Identifiant client du pro
     * @return {object} 							Objet courant
     */
    public function proGet($id=null, $custom_id=null) {
        $this->init('pro/get');
        $datas = array();
        self::datasAddInt('id', $id, $datas);
        self::datasAddString('custom_id', $custom_id, $datas);
        return $this->run(self::METHOD_GET, $datas);
    }

    /**
     * Appel du service pro/list : récupére les informations de tous les comptes pros liés à une société, ou liée à toutes les sociétés
     * Pour une marque, ne pas renseigner company_id et company_custom_id revient à récupérer tous les pros
     * Pour une société, il est inutile de renseigner les paramètres company_id et company_custom_id, ils ne seront pas utilisés
     * @access public
     * @param {int|null} $company_id				Identifiant immodvisor de la société
     * @param {int|string|null} $company_custom_id	Identifiant client de la société
     * @param {bool} $enable						Si true ne retourne que les comptes pros actifs sur les sociétés activés, si false retourne tous les comptes pros
     * @return {object} 							Objet courant
     */
    public function proList($company_id=null, $company_custom_id=null, $enable=true) {
        $this->init('pro/list');
        $datas = array();
        self::datasAddInt('company_id', $company_id, $datas);
        self::datasAddString('company_custom_id', $company_custom_id, $datas);
        self::datasAddBool('enable', $enable, $datas);
        return $this->run(self::METHOD_GET, $datas);
    }

    /**
     * Appel du service pro/create : crée un compte pro et l'associe à une société
     * Pour une marque, ne pas renseigner company_id et company_custom_id revient à associer le compte pro à la marque (ce qui donne un accès à toutes les sociétés de la marque)
     * Pour une société, il est inutile de renseigner les paramètres company_id et company_custom_id, ils ne seront pas utilisés
     * @access public
     * @param {int|null} $company_id				Identifiant immodvisor de la société
     * @param {int|string|null} $company_custom_id	Identifiant client de la société
     * @param {array} $datas						Données du pro. Dans l'ordre : email (obligatoire), custom_id, firstname, lastname, job, phone, mobile, address, address2, zipcode, city, acl_aapp (obligatoire), acl_pro (obligatoire)
     * @return {object} 							Objet courant
     */
    public function proCreate($company_id=null, $company_custom_id=null, $datas=array()) {
        $this->init('pro/create');
        if(!array_key_exists('email', $datas) || !Utils::isEmail($datas['email'])) {
            $this->content = false;
            return $this;
        }
        if(!array_key_exists('acl_aapp', $datas)) {
            $this->content = false;
            return $this;
        }
        if(!array_key_exists('acl_pro', $datas)) {
            $this->content = false;
            return $this;
        }
        if(is_object($datas)) {
            $datas = (array) $datas;
        }
        if(!is_array($datas)) {
            $datas = array();
        }
        $temp = array();
        self::datasAddInt('company_id', $company_id, $temp);
        self::datasAddString('company_custom_id', $company_custom_id, $temp);
        $datas = array_merge($temp, $datas);
        return $this->run(self::METHOD_POST, $datas);
    }

    /**
     * Appel du service pro/create : modifie un compte pro
     * @access public
     * @param {int} $id								Identifiant immodvisor du pro (obligatoire)
     * @param {int|string|null} $custom_id			Identifiant client du pro
     * @param {array} $datas						Données à modifier. Dans l'ordre : email, custom_id, enable, firstname, lastname, job, phone, mobile, address, address2, zipcode, city, acl_aapp, acl_pro
     * @return {object} 							Objet courant
     */
    public function proUpdate($id=null, $custom_id=null, $datas=array()) {
        $this->init('pro/update');
        if(!Utils::isInt($id)) {
            $this->content = false;
            return $this;
        }
        if(is_object($datas)) {
            $datas = (array) $datas;
        }
        if(!is_array($datas)) {
            $datas = array();
        }
        $temp = array();
        self::datasAddInt('id', $id, $temp);
        self::datasAddString('cid', $custom_id, $temp);
        $datas = array_merge($temp, $datas);
        return $this->run(self::METHOD_PUT, $datas);
    }

    /**
     * Appel du service pro/link : associe un compte pro à une société
     * Seules les marques peuvent utiliser ce service
     * Ne pas renseigner company_id et company_custom_id revient à associer le compte pro à la marque (ce qui donne un accès à toutes les sociétés de la marque)
     * @access public
     * @param {int} $id								Identifiant immodvisor du pro (obligatoire)
     * @param {int|string|null} $custom_id			Identifiant client du pro
     * @param {int|null} $company_id				Identifiant immodvisor de la société
     * @param {int|string|null} $company_custom_id	Identifiant client de la société
     * @return {object} 							Objet courant
     */
    public function proLink($id=null, $custom_id=null, $company_id=null, $company_custom_id=null) {
        $this->init('pro/link');
        if(!Utils::isInt($id)) {
            $this->content = false;
            return $this;
        }
        $datas = array();
        self::datasAddInt('id', $id, $datas);
        self::datasAddString('custom_id', $custom_id, $datas);
        self::datasAddInt('company_id', $company_id, $datas);
        self::datasAddString('company_custom_id', $company_custom_id, $datas);
        return $this->run(self::METHOD_POST, $datas);
    }

    /**
     * Appel du service pro/unlink : dissocie un compte pro d'une société
     * Seules les marques peuvent utiliser ce service. Il n'est pas possible de supprimer la dernière association du pro à une société du parc
     * company_id et/ou company_custom_id est obligatoire
     * @access public
     * @param {int} $id								Identifiant immodvisor du pro (obligatoire)
     * @param {int|string|null} $custom_id			Identifiant client du pro
     * @param {int|null} $company_id				Identifiant immodvisor de la société
     * @param {int|string|null} $company_custom_id	Identifiant client de la société
     * @return {object} 							Objet courant
     */
    public function proUnlink($id=null, $custom_id=null, $company_id=null, $company_custom_id=null) {
        $this->init('pro/unlink');
        if(!Utils::isInt($id)) {
            $this->content = false;
            return $this;
        }
        $datas = array();
        self::datasAddInt('id', $id, $datas);
        self::datasAddString('custom_id', $custom_id, $datas);
        self::datasAddInt('company_id', $company_id, $datas);
        self::datasAddString('company_custom_id', $company_custom_id, $datas);
        return $this->run(self::METHOD_DELETE, $datas);
    }

    /**
     * Appel du service review/list : recherche des avis
     * Pour une marque, identifier une société via les paramètres company_id et/ou company_custom_id permet de rechercher les avis d'une société précise
     * Pour une société, il est inutile de renseigner les paramètres company_id et company_custom_id, ils ne seront pas utilisés
     * @access public
     * @param {int|null} $company_id				Identifiant immodvisor de la société
     * @param {int|string|null} $company_custom_id	Identifiant client de la société
     * @param {date|null} $date_start				Date de début de la recherche (YYYY ou YYYY-MM ou YYYY-MM-DD)
     * @param {date|null} $date_stop				Date de fin de la recherche (YYYY ou YYYY-MM ou YYYY-MM-DD)
     * @return {object} 							Objet courant
     */
    public function reviewList($company_id=null, $company_custom_id=null, $date_start=null, $date_stop=null) {
        $this->init('review/list');
        $datas = array();
        self::datasAddInt('company_id', $company_id, $datas);
        self::datasAddString('company_custom_id', $company_custom_id, $datas);
        if(!empty($date_start)) {
            if(preg_match('`^[0-9]+$`', $date_start)) {
                if(empty($date_stop)) {
                    $date_stop = $date_start;
                }
                $date_start .= "-01-01";
            } elseif(preg_match('`^[0-9]{4}-[0-9]{2}$`', $date_start)) {
                if(empty($date_stop)) {
                    $date_stop = $date_start;
                }
                $date_start .= "-01";
            }
            self::datasAddString('date_start', $date_start, $datas);
        }
        if(!empty($date_stop)) {
            if(preg_match('`^[0-9]+$`', $date_stop)) {
                $date_stop .= "-12-31";
            } elseif(preg_match('`^[0-9]{4}-[0-9]{2}$`', $date_stop)) {
                $date_stop .= "-".cal_days_in_month(CAL_GREGORIAN, (int) substr($date_stop, 5), (int) substr($date_stop, 0, 4));
            }
            self::datasAddString('date_stop', $date_stop, $datas);
        }
        return $this->run(self::METHOD_GET, $datas);
    }

    /**
     * Appel du service review/collect : lance la collecte d'un avis
     * Pour une marque, identifier une société via les paramètres company_id et/ou company_custom_id est obligatoire
     * Pour une société, il est inutile de renseigner les paramètres company_id et company_custom_id, ils ne seront pas utilisés
     * @access public
     * @param {int|null} $company_id				Identifiant immodvisor de la société
     * @param {int|string|null} $company_custom_id	Identifiant client de la société
     * @param {string} $email						Adresse email du dépositaire (obligatoire)
     * @param {string} $mobile						Numéro mobile du dépositaire
     * @param {string|null} $firstname				Prénom du dépositaire
     * @param {string|null} $lastname				Nom du dépositaire
     * @param {int|null} $pro_id					Identifiant immodvisor du professionnel à l'origine de la transaction
     * @param {int|null} $pro_custom_id				Identifiant client du professionnel à l'origine de la transaction
     * @param {int|null} $activity_id				Identifiant immodvisor du métier lié à l'avis. N'est utilisé que si votre e-vitrine est associée à plusieurs métiers
     * @param {int|null} $highlight_number			Numéro du temps fort associé au métier (ne fonctionne que si les temps forts sont activés sur le compte)
     * @param {string|null} $custom_ref				Référence client de la sollicitation
     * @param {int|null} $folder_id                 Identifiant du dossier/programme associé
     * @return {object} 							Objet courant
     */
    public function reviewCollect($company_id=null, $company_custom_id=null, $email=null, $mobile=null, $firstname=null, $lastname=null, $pro_id=null, $pro_custom_id=null, $activity_id=null, $highlight_number=null, $custom_ref=null, $folder_id=null) {
        $this->init('review/collect');
        if(!Utils::isEmail($email)) {
            $this->content = false;
            return $this;
        }
        $datas = array();
        self::datasAddInt('company_id', $company_id, $datas);
        self::datasAddString('company_custom_id', $company_custom_id, $datas);
        self::datasAddString('email', $email, $datas);
        self::datasAddString('mobile', $mobile, $datas);
        self::datasAddString('firstname', $firstname, $datas);
        self::datasAddString('lastname', $lastname, $datas);
        self::datasAddInt('pro_id', $pro_id, $datas);
        self::datasAddString('pro_custom_id', $pro_custom_id, $datas);
        self::datasAddInt('activity_id', $activity_id, $datas);
        self::datasAddInt('highlight_number', $highlight_number, $datas);
        self::datasAddString('custom_ref', $custom_ref, $datas);
        self::datasAddInt('folder_id', $folder_id, $datas);
        return $this->run(self::METHOD_POST, $datas);
    }

    /**
     * Appel du service review/collectmultiple : lance la collecte de plusieurs avis
     * @access public
     * @param {array} $datas						Liste des dépositaires, chaque entrée de tableau est lui même un tableau ou un objet contenant : company_id (obligatoire et/ou), company_custom_id (obligatoire et/ou), email (obligatoire), firstname, lastname, pro_id, activity_id, highlight_number
     * @return {object} 							Objet courant
     */
    public function reviewCollectMultiple($datas=array()) {
        $this->init('review/collectmultiple');
        if(!is_array($datas) || empty($datas)) {
            $this->content = false;
            return $this;
        }
        $datas_sents = array();
        foreach($datas as $k => $v) {
            if(is_object($v)) {
                $v = (array) $v;
            }
            if(!is_array($v)) {
                continue;
            }
            if((!array_key_exists('company_id', $v) || !Utils::isInt($v['company_id'])) && (!array_key_exists('company_custom_id', $v) || empty($v['company_custom_id']))) {
                continue;
            }
            if(!array_key_exists('email', $v) || !Utils::isEmail($v['email'])) {
                continue;
            }
            $sent = array();
            if(array_key_exists('company_id', $v) && Utils::isInt($v['company_id'])) {
                self::datasAddString('company_id', $v['company_id'], $sent);
            }
            if(array_key_exists('company_custom_id', $v) && !empty($v['company_custom_id'])) {
                self::datasAddString('company_custom_id', $v['company_custom_id'], $sent);
            }
            self::datasAddString('email', $v['email'], $sent);
            if(array_key_exists('mobile', $v) && !empty($v['mobile'])) {
                self::datasAddString('mobile', $v['mobile'], $sent);
            }
            if(array_key_exists('firstname', $v) && !empty($v['firstname'])) {
                self::datasAddString('firstname', $v['firstname'], $sent);
            }
            if(array_key_exists('lastname', $v) && !empty($v['lastname'])) {
                self::datasAddString('lastname', $v['lastname'], $sent);
            }
            if(array_key_exists('pro_id', $v) && Utils::isInt($v['pro_id'])) {
                self::datasAddString('pro_id', $v['pro_id'], $sent);
            }
            if(array_key_exists('pro_custom_id', $v) && $v['pro_custom_id'] !== null) {
                self::datasAddString('pro_custom_id', $v['pro_custom_id'], $sent);
            }
            if(array_key_exists('activity_id', $v) && Utils::isInt($v['activity_id'])) {
                self::datasAddString('activity_id', $v['activity_id'], $sent);
            }
            if(array_key_exists('highlight_number', $v) && Utils::isInt($v['highlight_number'])) {
                self::datasAddString('highlight_number', $v['highlight_number'], $sent);
            }
            if(array_key_exists('custom_ref', $v) && $v['custom_ref'] !== null) {
                self::datasAddString('custom_ref', $v['custom_ref'], $sent);
            }
            if(array_key_exists('folder_id', $v) && Utils::isInt($v['folder_id'])) {
                self::datasAddString('folder_id', $v['folder_id'], $sent);
            }
            $datas_sents[] = $sent;
            unset($sent);
        }
        $datas = array();
        if(empty($datas_sents) || count($datas_sents) > 500) {
            $this->content = false;
            return $this;
        }
        self::datasAddArray('datas', $datas_sents, $datas);
        return $this->run(self::METHOD_POST, $datas);
    }

    /**
     * Appel du service team/get : récupère les informations d'une équipe
     * @access public
     * @param {int} $id								Identifiant immodvisor de l'équipe
     * @return {object} 							Objet courant
     */
    public function teamGet($id=null) {
        $this->init('team/get');
        if(!Utils::isInt($id)) {
            $this->content = false;
            return $this;
        }
        $datas = array();
        self::datasAddInt('id', $id, $datas);
        return $this->run(self::METHOD_GET, $datas);
    }

    /**
     * Appel du service team/list : récupère les informations de toutes les équipes de la e-vitrine
     * @access public
     * @param {int|null} $company_id				Identifiant immodvisor de la société
     * @param {int|string|null} $company_custom_id	Identifiant client de la société
     * @return {object} 							Objet courant
     */
    public function teamList($company_id=null, $company_custom_id=null) {
        $this->init('team/list');
        $datas = array();
        self::datasAddInt('company_id', $company_id, $datas);
        self::datasAddString('company_custom_id', $company_custom_id, $datas);
        return $this->run(self::METHOD_GET, $datas);
    }

    /**
     * Appel du service team/create : création d'une équipe pour une e-vitrine
     * @access public
     * @param {int|null} $company_id				Identifiant immodvisor de la société
     * @param {int|string|null} $company_custom_id	Identifiant client de la société
     * @param {string} $name						Nom de l'équipe
     * @return {object} 							Objet courant
     */
    public function teamCreate($company_id=null, $company_custom_id=null, $name=null, $custom_id=null) {
        $this->init('team/create');
        $datas = array();
        self::datasAddInt('company_id', $company_id, $datas);
        self::datasAddString('company_custom_id', $company_custom_id, $datas);
        self::datasAddString('name', $name, $datas);
        self::datasAddString('custom_id', $custom_id, $datas);
        return $this->run(self::METHOD_POST, $datas);
    }

    /**
     * Appel du service team/update : met à jour une équipe
     * @access public
     * @param {int} $id								Identifiant immodvisor de l'équipe
     * @param {string|null} $name					Nom de l'équipe
     * @return {object} 							Objet courant
     */
    public function teamUpdate($id=null, $name=null, $custom_id=null) {
        $this->init('team/update');
        if(!Utils::isInt($id)) {
            $this->content = false;
            return $this;
        }
        $datas = array();
        self::datasAddInt('id', $id, $datas);
        self::datasAddString('name', $name, $datas);
        self::datasAddString('custom_id', $custom_id, $datas);
        return $this->run(self::METHOD_PUT, $datas);
    }

    /**
     * Appel du service team/delete : supprime une équipe
     * @access public
     * @param {int} $id								Identifiant immodvisor de l'équipe
     * @return {object} 							Objet courant
     */
    public function teamDelete($id) {
        $this->init('team/delete');
        if(!Utils::isInt($id)) {
            $this->content = false;
            return $this;
        }
        $datas = array();
        self::datasAddInt('id', $id, $datas);
        return $this->run(self::METHOD_DELETE, $datas);
    }

    /**
     * Appel du service team/prolink : associe un compte pro à une équipe
     * @access public
     * @param {int} $id								Identifiant immodvisor de l'équipe (obligatoire)
     * @param {int|string|null} $custom_id			Identifiant client de l'équipe
     * @param {int|null} $pro_id					Identifiant immodvisor du compte pro (obligatoire)
     * @param {int|string|null} $pro_custom_id		Identifiant client du compte pro
     * @return {object} 							Objet courant
     */
    public function teamProLink($id=null, $custom_id=null, $pro_id=null, $pro_custom_id=null) {
        $this->init('team/prolink');
        if(!Utils::isInt($id)) {
            $this->content = false;
            return $this;
        }
        if(!Utils::isInt($pro_id)) {
            $this->content = false;
            return $this;
        }
        $datas = array();
        self::datasAddInt('id', $id, $datas);
        self::datasAddString('custom_id', $custom_id, $datas);
        self::datasAddInt('pro_id', $pro_id, $datas);
        self::datasAddString('pro_custom_id', $pro_custom_id, $datas);
        return $this->run(self::METHOD_POST, $datas);
    }

    /**
     * Appel du service team/prounlink : dissocie un compte pro à une équipe
     * @access public
     * @param {int} $id								Identifiant immodvisor de l'équipe (obligatoire)
     * @param {int|string|null} $custom_id			Identifiant client de l'équipe
     * @param {int|null} $pro_id					Identifiant immodvisor du compte pro (obligatoire)
     * @param {int|string|null} $pro_custom_id		Identifiant client du compte pro
     * @return {object} 							Objet courant
     */
    public function teamProUnlink($id=null, $custom_id=null, $pro_id=null, $pro_custom_id=null) {
        $this->init('team/prounlink');
        if(!Utils::isInt($id)) {
            $this->content = false;
            return $this;
        }
        if(!Utils::isInt($pro_id)) {
            $this->content = false;
            return $this;
        }
        $datas = array();
        self::datasAddInt('id', $id, $datas);
        self::datasAddString('custom_id', $custom_id, $datas);
        self::datasAddInt('pro_id', $pro_id, $datas);
        self::datasAddString('pro_custom_id', $pro_custom_id, $datas);
        return $this->run(self::METHOD_DELETE, $datas);
    }


    /**
     * Apper du service google/collect : lance la collecte d'un avis Google
     * Pour une marque, identifier une société via les paramètres company_id et/ou company_custom_id est obligatoire
     * Pour une société, il est inutile de renseigner les paramètres company_id et company_custom_id, ils ne seront pas utilisés
     * @access public
     * @param {int|null} $company_id				Identifiant immodvisor de la société
     * @param {int|string|null} $company_custom_id	Identifiant client de la société
     * @param {string} $email						Adresse email du dépositaire (obligatoire)
     * @param {string|null} $mobile				    Numéro mobile du dépositaire
     * @param {string|null} $type                   Type d'envoie : mail ou sms (par défaut mail)
     * @param {array|null} $sms_content             Uniquement pour l'envoie de sms, tableau : nom envoyeur et message sms
     * @param {string|null} $sending_date           Date d'envoi de la collecte
     * @return {object} 							Objet courant
     */
	public function googleCollect($company_id=null, $company_custom_id=null, $email=null, $mobile=null, $type=null, $sms_content=null, $sending_date=null) {
        $this->init('google/collect');
        if(!Utils::isEmail($email)) {
            $this->content = false;
            return $this;
        }
        $datas = array();
        self::datasAddInt('company_id', $company_id, $datas);
        self::datasAddString('company_custom_id', $company_custom_id, $datas);
        self::datasAddString('email', $email, $datas);
        self::datasAddString('mobile', $mobile, $datas);
        self::datasAddString('type', $type, $datas);
        self::datasAddArray('sms_content', $sms_content, $datas);
        self::datasAddString('sending_date', $sending_date, $datas);
        return $this->run(self::METHOD_POST, $datas);
    }

    /**
     * Appel du service google/collectmultiple : lance la collecte de plusieurs avis google
     * @access public
     * @param {array} $datas						Liste des dépositaires, chaque entrée de tableau est lui même un tableau ou un objet contenant : company_id (obligatoire et/ou), company_custom_id (obligatoire et/ou), email (obligatoire), mobile, type, params
     * @return {object} 							Objet courant
     */
    public function googleCollectMultiple($datas=array()) {
        $this->init('google/collectmultiple');
        if(!is_array($datas) || empty($datas)) {
            $this->content = false;
            return $this;
        }
        $datas_sents = array();
        foreach($datas as $k => $v) {
            if(is_object($v)) {
                $v = (array) $v;
            }
            if(!is_array($v)) {
                continue;
            }
            $sent = array();
            if(array_key_exists('company_id', $v) && Utils::isInt($v['company_id'])) {
                self::datasAddString('company_id', $v['company_id'], $sent);
            }
            if(array_key_exists('company_custom_id', $v) && !empty($v['company_custom_id'])) {
                self::datasAddString('company_custom_id', $v['company_custom_id'], $sent);
            }
            self::datasAddString('email', $v['email'], $sent);
            if(array_key_exists('mobile', $v) && !empty($v['mobile'])) {
                self::datasAddString('mobile', $v['mobile'], $sent);
            }
            if(array_key_exists('type', $v) && !empty($v['type'])) {
                self::datasAddString('type', $v['type'], $sent);
            }
            if(array_key_exists('sms_content', $v) && !empty($v['sms_content'])) {
                self::datasAddArray('sms_content', $v['sms_content'], $sent);
            }
            if(array_key_exists('sending_date', $v) && !empty($v['sending_date'])) {
                self::datasAddString('sending_date', $v['sending_date'], $sent);
            }
            $datas_sents[] = $sent;
            unset($sent);
        }
        $datas = array();
        if(empty($datas_sents) || count($datas_sents) > 2000) {
            $this->content = false;
            return $this;
        }
        self::datasAddArray('datas', $datas_sents, $datas);
        return $this->run(self::METHOD_POST, $datas);
    }


    /**
     * Appel du service folder/create : création d'un dossier/programme pour une vitrine
     * @access public
     * @param {int} $company_id				        Identifiant immodvisor de la société (obligatoire)
     * @param {int|string|null} $company_custom_id	Identifiant client de la société
     * @param {string} name	                        Nom du dossier/programme (obligatoire)
     * @param {string|null} reference				Référence cliente du dossier/programme
     * @param {string|null} status                  Statut du dossier/programme
     * @param {string|null} start_date              Date de début du dossier/programme (YYYY-MM-DD ou YYYY-MM)
     * @param {string|null} end_date                Date de fin du dossier/programme (YYYY-MM-DD ou YYYY-MM)
     * @return {object} 							Objet courant
     */
    public function folderCreate($company_id=null, $company_custom_id=null, $name=null, $reference=null, $status=null, $start_date=null, $end_date=null) {
        $this->init('folder/create');
        $datas = array();
        self::datasAddInt('company_id', $company_id, $datas);
        self::datasAddString('company_custom_id', $company_custom_id, $datas);
        self::datasAddString('name', $name, $datas);
        self::datasAddString('reference', $reference, $datas);
        self::datasAddString('status', $status, $datas);
        self::datasAddString('start_date', $start_date, $datas);
        self::datasAddString('end_date', $end_date, $datas);
        return $this->run(self::METHOD_POST, $datas);
    }

    /**
     * Appel du service folder/update : met à jour un dossier/programme
     * @access public
     * @param {int} $id								Identifiant immodvisor du dossier (obligatoire)
     * @param {string} name	                        Nom du dossier/programme (obligatoire)
     * @param {string|null} reference				Référence cliente du dossier/programme
     * @param {string|null} status                  Statut du dossier/programme
     * @param {string|null} start_date              Date de début du dossier/programme (YYYY-MM-DD ou YYYY-MM)
     * @param {string|null} end_date                Date de fin du dossier/programme (YYYY-MM-DD ou YYYY-MM)
     * @return {object} 							Objet courant
     */
    public function folderUpdate($id=null, $name=null, $reference=null, $status=null, $start_date=null, $end_date=null) {
        $this->init('folder/update');
        $datas = array();
        self::datasAddInt('id', $id, $datas);
        self::datasAddString('name', $name, $datas);
        self::datasAddString('reference', $reference, $datas);
        self::datasAddString('status', $status, $datas);
        self::datasAddString('start_date', $start_date, $datas);
        self::datasAddString('end_date', $end_date, $datas);
        return $this->run(self::METHOD_PUT, $datas);
    }

    /**
     * Appel du service folder/delete : supprime un dossier/programme
     * @access public
     * @param {int} $id								Identifiant immodvisor du dossier/programme (obligatoire)
     * @return {object} 							Objet courant
     */
    public function folderDelete($id=null) {
        $this->init('folder/delete');
        $datas = array();
        self::datasAddInt('id', $id, $datas);
        return $this->run(self::METHOD_DELETE, $datas);
    }

    /**
     * Appel du service folder/get : récupère les informations d'un dossier/programme
     * @access public
     * @param {int} $id								Identifiant immodvisor du dossier/programme (obligatoire)
     * @return {object}                             Object courant
     */
    public function folderGet($id=null) {
        $this->init('folder/get');
        $datas = array();
        self::datasAddInt('id', $id, $datas);
        return $this->run(self::METHOD_GET, $datas);
    }

    /**
     * Appel du service folder/list : récupère les informations de tous les dossiers d'une vitrine
     * @access public
     * @param {int} $company_id                     Identifiant immodvisor de la société (obligatoire
     * @param {int|string|null} $company_custom_id	Identifiant client de la société
     * @return {object}                             Object courant
     */
    public function folderList($company_id=null, $company_custom_id=null) {
        $this->init('folder/list');
        $datas = array();
        self::datasAddInt('company_id', $company_id, $datas);
        self::datasAddString('company_custom_id', $company_custom_id, $datas);
        return $this->run(self::METHOD_GET, $datas);
    }

    /**
     * Formate les entiers pour les inputs des services
     * @access protected
     * @param {string} $k							Nom de l'input
     * @param {mixed} $v							Valeur de l'input
     * @param {array} $datas						Tableau d'inputs à remplir
     */
    protected static function datasAddInt($k, $v, &$datas, $unsigned=true) {
        if(Utils::isInt($v, $unsigned)) {
            $datas[$k] = (int) $v;
        }
    }

    /**
     * Formate les flotants pour les inputs des services
     * @access protected
     * @param {string} $k							Nom de l'input
     * @param {mixed} $v							Valeur de l'input
     * @param {array} $datas						Tableau d'inputs à remplir
     */
    protected static function datasAddFloat($k, $v, &$datas, $unsigned=true) {
        if(Utils::isFloat($v, $unsigned)) {
            $datas[$k] = (float) $v;
        }
    }

    /**
     * Formate les chaines de caractères pour les inputs des services
     * @access protected
     * @param {string} $k							Nom de l'input
     * @param {mixed} $v							Valeur de l'input
     * @param {array} $datas						Tableau d'inputs à remplir
     */
    protected static function datasAddString($k, $v, &$datas) {
        if(!is_array($v) && !is_object($v)) {
            $v = trim(strip_tags($v));
            if(!empty($v)) {
                $datas[$k] = $v;
            }
        }
    }

    /**
     * Formate les booléens pour les inputs des services
     * @access protected
     * @param {string} $k							Nom de l'input
     * @param {mixed} $v							Valeur de l'input
     * @param {array} $datas						Tableau d'inputs à remplir
     */
    protected static function datasAddBool($k, $v, &$datas) {
        if($v !== null && !is_array($v) && !is_object($v)) {
            if($v === true || $v === 1 || $v === '1') {
                $datas[$k] = 1;
            } elseif($v === false || $v === 0 || $v === '0') {
                $datas[$k] = 0;
            }
        }
    }

    /**
     * Formate les tableaux pour les inputs des services
     * @access protected
     * @param {string} $k							Nom de l'input
     * @param {mixed} $v							Valeur de l'input
     * @param {array} $datas						Tableau d'inputs à remplir
     */
    protected static function datasAddArray($k, $v, &$datas) {
        if(is_object($v)) {
            $v = (array) $v;
        }
        if(is_array($v)) {
            $datas[$k] = $v;
        }
    }

    /**
     * Effectue l'appel d'un webservice immodvisor
     * @access protected
     * @param {string} $method						Méthode utilisée
     * @param {array} $datas						Paramètres en envoyer
     * @return {object} 							Objet courant
     */
    protected function run($method, $datas=array()) {
        $url = $this->getUrlApi().$this->service;
        $checksum = $this->calcChecksumIn($datas);
        $fields = $datas;
        if($this->output_format !== null) {
            $fields['format'] = $this->output_format;
        }
        if($this->debug) {
            $fields['debug'] = 1;
        }
        $fields['checksum'] = $checksum;
        if($method == self::METHOD_GET) {
            $url .= (strpos($url, '?') === false) ? "?" : "&";
            $url .= http_build_query($fields);
        }
        $referer = ($this->referer === null) ? Utils::getReferer() : $this->referer;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('APIKEY: '.$this->api_key, 'APIVERSION: '.parent::VERSION));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $verifypeer = (substr($referer, 0, 5) == 'https') ? true : false;
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verifypeer);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2) AppleWebKit/536.6 (KHTML, like Gecko) Chrome/20.0.1090.0 Safari/536.6');
        switch($method) {
            case self::METHOD_POST:
            case self::METHOD_PUT:
            case self::METHOD_DELETE:
                $http_build_query = http_build_query($fields);
                foreach($fields as $k => $v) {
                    if($v === null) {
                        if(!empty($http_build_query)) {
                            $http_build_query .= '&';
                        }
                        $http_build_query .= $k."=";
                    }
                }
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($ch, CURLOPT_POST, count($fields));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $http_build_query);
                break;
        }
        $this->content = curl_exec($ch);
        if($this->content === false) {
            $this->ch_error = curl_error($ch);
        }
        $this->http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->url_real = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        return $this;
    }

    /**
     * Calcule le checksum à envoyer lors des appels des webservices
     * @access private
     * @param {array} $datas						Paramètres en envoyer au service
     * @return {string} 							Checksum à envoyer
     */
    private function calcChecksumIn($datas=array()) {
        $checksum = "";
        $checksum .= $this->api_key;
        foreach($datas as $k => $v) {
            if(is_array($v) || is_object($v)) {
				$v = json_encode($v, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            } elseif($v === true) {
                $v = 1;
            } elseif($v === false) {
                $v = 0;
            }
            $checksum .= $v;
        }
        $checksum .= $this->checksum_salt_in;
        $checksum .= $this->output_format;
        $checksum .= ($this->debug) ? '1' : '';
        return sha1($checksum);
    }

    /**
     * Parse le contenu retourné par le dernier service appelé pour en retourner un objet
     * @access private
     * @return {object|false} 						Objet contenant le retour du dernier service appelé, false en cas de problème
     */
    public function parse() {
        switch($this->output_format) {
            case self::OUTPUT_FORMAT_JSON:
                return $this->parseJson();
            case self::OUTPUT_FORMAT_XML:
                return $this->parseXml();
        }
        return false;
    }

    /**
     * Parse un contenu json retourné par le dernier service appelé pour en retourner un objet
     * @access private
     * @return {object|false} 						Objet contenant le retour du dernier service appelé, false en cas de problème
     */
    private function parseJson() {
        if($this->content === null || $this->content === false || !is_string($this->content)) {
            return false;
        }
        return json_decode($this->content);
    }

    /**
     * Parse un contenu xml retourné par le dernier service appelé pour en retourner un objet
     * @access private
     * @return {object|false} 						Objet contenant le retour du dernier service appelé, false en cas de problème
     */
    private function parseXml() {
        if($this->content === null || $this->content === false || !is_string($this->content)) {
            return false;
        }
        return new \SimpleXMLElement($this->content);
    }

    /**
     * Effectue toutes les vérifications nécessaires suite à l'appel d'un service
     * @access private
     * @return {bool} 								True si tout est bon, false sinon. Dans ce cas le message d'erreur peut être récupéré en appelant la méthode getError()
     */
    public function check() {
        if($this->content === null || $this->content === false || !is_string($this->content)) {
            $this->error = ($this->ch_error !== null) ? "Erreur curl : ".$this->ch_error : "Pas de contenu.";
            return false;
        }
        $content = $this->parse($this->content);
        if(!is_object($content)) {
            $this->error = "Contenu retourné invalide.";
            return false;
        }
        if($this->http_code != 200) {
            $this->error = "Http code différent de 200.";
            if($this->http_code == 503) {
                $this->error = "Api en maintenance.";
            } elseif($this->http_code == 400) {
                $this->error = "Message d'erreur du service.";
                $temp = trim((string) $content->error);
                if(!empty($temp)) {
                    $this->error = $temp;
                }
            }
            return false;
        }
        $status = trim((string) $content->status);
        if($status != self::STATUS_DONE) {
            $this->error = "Status non validé.";
            if($status == self::STATUS_MAINTENANCE) {
                $this->error = "Api en maintenance.";
            } elseif($status == self::STATUS_ERROR) {
                $this->error = "Message d'erreur du service.";
                $temp = trim((string) $content->error);
                if(!empty($temp)) {
                    $this->error = $temp;
                }
            }
            return false;
        }
        $checksum = (string) $content->checksum;
        if(empty($checksum)) {
            $this->error = "Checksum non récupéré.";
            return false;
        }
        if(self::calcChecksumOut($content) != $checksum) {
            $this->error = "Checksum invalide.";
            return false;
        }
        return true;
    }

    /**
     * Calcule le checksum des données récupérées d'un webservice
     * @access private
     * @param {object} $content						Données
     * @return {string} 							Checksum à vérifier
     */
    private function calcChecksumOut(&$content) {
        $checksum = "";
        $checksum .= (string) $content->status;
        $checksum .= (string) $content->error;
        $checksum .= ((string) $content->status == self::STATUS_DONE && (is_object($content->datas) || is_array($content->datas))) ? $this->calcChecksumOutDatas($content->datas) : '';
        $checksum .= $this->checksum_salt_out;
        return sha1($checksum);
    }

    /**
     * Calcule une partie du checksum
     * @access private
     * @param {object} $datas						Données brutes
     * @return {string} 							Chaîne à intégrer au calcul du checksum
     */
    private function calcChecksumOutDatas(&$datas) {
        if(!is_array($datas) && !is_object($datas)) {
            return "";
        }
        $return = "";
        foreach($datas as $k => $v) {
            if($this->output_format == self::OUTPUT_FORMAT_XML || (!is_array($v) && !is_object($v))) {
                $return .= trim((string) $v);
            }
            if(is_array($v) || is_object($v)) {
                $return .= (string) $this->calcChecksumOutDatas($v);
            }
        }
        return $return;
    }

}
