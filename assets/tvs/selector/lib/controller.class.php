<?php namespace Selector;
/**
 * Created by PhpStorm.
 * User: Pathologic
 * Date: 27.05.2015
 * Time: 8:08
 */

class SelectorController {
    protected $modx = null;
    public $dlParams = array(
        'api'           =>  'id,pagetitle,parent,html,text',
        'JSONformat'    =>  'old',
        'display'       =>  10,
        'offset'        =>  0,
        'sortBy'        =>  'id',
        'sortDir'       =>  'desc',
        'parents'       =>  0,
        'showParent'    =>  1,
        'depth'         =>  10,
        'searchContentFields'  =>  'id,pagetitle,longtitle',
        'searchTVFields'    => '',
        'textField'    => 'pagetitle',
        'prepare'      => 'Selector\SelectorController::prepare'
    );
    public function __construct($modx) {
        $this->modx = $modx;
    }

    public function callExit(){
        if($this->isExit){
            echo $this->output;
            exit;
        }
    }

    public static function prepare(array $data = array(), \DocumentParser $modx, $_DL, \prepare_DL_Extender $_extDocLister)
    {
        if(($data['parentName']=$_extDocLister->getStore('parentName'.$data['parent'])) === null){
            $q = $modx->db->query("SELECT pagetitle FROM ".$modx->getFullTableName('site_content')." WHERE id = '".$data['parent']."'");
            $data['parentName'] = $modx->db->getValue($q);
            $_extDocLister->setStore('parentName'.$data['parent'], $data['parentName']);
        }

        if(($docCrumbs=$_extDocLister->getStore('currentParents'.$data['parent'])) === null){
            $modx->documentObject['id'] = $data['id'];
            $docCrumbs = rtrim($modx->runSnippet('DLcrumbs', array(
                'ownerTPL' => '@CODE:[+crumbs.wrap+]',
                'tpl' => '@CODE: [+title+] /',
                'tplCurrent' => '@CODE: [+title+] /',
                'hideMain' => '1'
            )),' /');
            $_extDocLister->setStore('currentParents'.$data['parent'], $docCrumbs);
        }
        $html =preg_replace("/(".preg_quote($_DL->getCFGDef('search'), "/").")/iu", "<b>$0</b>", $data['pagetitle']);
        $data['text'] = "{$data['id']}. {$data['pagetitle']}";
        $data['html'] = "<div><small>{$docCrumbs}</small><br>{$data['id']}. {$html}</div>";
        return $data;
    }

    public function listing() {
        $search = is_scalar($_REQUEST['search']) ? $_REQUEST['search'] : '';
            if (!empty($search)) {
                $this->dlParams['search'] = $search;
                $searchContentFields = explode(',', $this->dlParams['searchContentFields']);
                $searchQuery = array();
                $addWhereList = isset($this->dlParams['addWhereList']) ? $this->dlParams['addWhereList'] : '';
                foreach ($searchContentFields as $field) {
                    $searchQuery[] = "c.{$field} LIKE '%{$search}%'";
                }
                $searchQuery = implode(' OR ', $searchQuery);
                if (is_numeric($search)) {
                    $idQuery = "c.id = {$search}";
                    $searchQuery = empty($searchQuery) ? $idQuery : "{$idQuery} OR {$searchQuery}";
                }
                $filters = array();
                $searchTVFields = explode(',', $this->dlParams['searchTVFields']);
                foreach ($searchTVFields as $tv) {
                    $filters[] = "tv:{$tv}:like:{$search}";
                }
                $filters = implode(';',$filters);
                if (!empty($filters)) {
                    $filters = "OR({$filters})";
                    $this->dlParams['filters'] = $filters;
                }
                $this->dlParams['addWhereList'] = empty($addWhereList) ? $searchQuery : "{$addWhereList} AND ({$searchQuery})";
            }
        return $this->modx->runSnippet("DocLister", $this->dlParams);

    }
}