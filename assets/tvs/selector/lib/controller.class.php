<?php namespace Selector;

/**
 * Created by PhpStorm.
 * User: Pathologic
 * Date: 27.05.2015
 * Time: 8:08
 */

class SelectorController
{
    protected $modx = null;
    public $dlParams = array(
        'api'                 => 'id,pagetitle,parent,html,text',
        'JSONformat'          => 'old',
        'display'             => 10,
        'offset'              => 0,
        'sortBy'              => 'id',
        'sortDir'             => 'desc',
        'parents'             => 0,
        'showParent'          => 1,
        'depth'               => 10,
        'searchContentFields' => 'id,pagetitle,longtitle',
        'searchTVFields'      => '',
        'textField'           => 'pagetitle',
        'addWhereList'        => 'c.published = 1',
        'prepare'             => 'Selector\SelectorController::prepare'
    );
    public $dlParamsNoSearch = array();

    /**
     * SelectorController constructor.
     * @param \DocumentParser $modx
     */
    public function __construct(\DocumentParser $modx)
    {
        $this->modx = $modx;
    }

    /**
     *
     */
    public function callExit()
    {
        if ($this->isExit) {
            echo $this->output;
            exit;
        }
    }

    /**
     * @param array $data
     * @param \DocumentParser $modx
     * @param \DocLister $_DL
     * @param \prepare_DL_Extender $_extDocLister
     * @return array
     */
    public static function prepare(
        array $data = array(),
        \DocumentParser $modx,
        \DocLister $_DL,
        \prepare_DL_Extender $_extDocLister
    ) {
        if (($data['parentName'] = $_extDocLister->getStore('parentName' . $data['parent'])) === null) {
            $q = $modx->db->query("SELECT pagetitle FROM " . $modx->getFullTableName('site_content') . " WHERE id = '" . $data['parent'] . "'");
            $data['parentName'] = $modx->db->getValue($q);
            $_extDocLister->setStore('parentName' . $data['parent'], $data['parentName']);
        }

        if (($docCrumbs = $_extDocLister->getStore('currentParents' . $data['parent'])) === null) {
            $modx->documentObject['id'] = $data['id'];
            $docCrumbs = rtrim($modx->runSnippet('DLcrumbs', array(
                'ownerTPL'   => '@CODE:[+crumbs.wrap+]',
                'tpl'        => '@CODE: [+title+] /',
                'tplCurrent' => '@CODE: [+title+] /',
                'hideMain'   => '1'
            )), ' /');
            $_extDocLister->setStore('currentParents' . $data['parent'], $docCrumbs);
        }
        $html = preg_replace("/(" . preg_quote($_DL->getCFGDef('search'), "/") . ")/iu", "<b>$0</b>",
            $data[$_DL->getCFGDef('textField', 'pagetitle')]);
        $data['text'] = "{$data['id']}. {$data[$_DL->getCFGDef('textField','pagetitle')]}";
        $data['html'] = "<div><small>{$docCrumbs}</small><br>{$data['id']}. {$html}</div>";

        return $data;
    }

    /**
     * @return string
     */
    public function listing()
    {
        $search = is_scalar($_REQUEST['search']) ? $_REQUEST['search'] : '';
        if (!empty($search)) {
            $this->dlParams['search'] = $search;
            $searchContentFields = explode(',', $this->dlParams['searchContentFields']);
            $filters = array();

            if (is_numeric($search)) {
                $filters[] = "content:id:=:{$search}";
            }

            foreach ($searchContentFields as $field) {
                $filters[] = "content:{$field}:like:{$search}";
            }

            $searchTVFields = explode(',', $this->dlParams['searchTVFields']);
            foreach ($searchTVFields as $tv) {
                $filters[] = "tv:{$tv}:like:{$search}";
            }
            $filters = implode(';', $filters);
            if (!empty($filters)) {
                $filters = "OR({$filters})";
                $this->dlParams['filters'] = $filters;
            }
        }

        return $this->modx->runSnippet("DocLister", $this->dlParams);
    }
}
