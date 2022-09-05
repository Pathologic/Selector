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
        'sortBy'              => 'c.id',
        'sortDir'             => 'desc',
        'parents'             => 0,
        'showParent'          => 1,
        'depth'               => 10,
        'searchContentFields' => 'c.id,c.pagetitle,c.longtitle',
        'searchTVFields'      => '',
        'idField'             => 'id',
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
        if (isset($this->isExit)) {
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
        array $data,
        \DocumentParser $modx,
        \DocLister $_DL,
        \prepare_DL_Extender $_extDocLister
    ) {
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
        if ($search = $_DL->getCFGDef('search')) {
            $html = preg_replace("/(" . preg_quote($search, "/") . ")/iu", "<b>$0</b>",
                $data[$_DL->getCFGDef('textField', 'pagetitle')]);
        } else {
            $html = $data[$_DL->getCFGDef('textField', 'pagetitle')];
        }
        $data['text'] = "{$data[$_DL->getCFGDef('idField','id')]}. {$data[$_DL->getCFGDef('textField','pagetitle')]}";
        $data['html'] = "<div><small>{$docCrumbs}</small><br>{$data['id']}. {$html}</div>";

        return $data;
    }

    /**
     * @return string
     */
    public function listing()
    {
        $search = isset($_REQUEST['search']) && is_scalar($_REQUEST['search']) ? $_REQUEST['search'] : '';
        if (!empty($search)) {
            if (substr($search, 0, 1) == '=') {
                $search = substr($search, 1);
                $mode = '=';
            } else {
                $mode = 'like';
            }
            $this->dlParams['search'] = $search;
            $searchContentFields = explode(',', $this->dlParams['searchContentFields']);
            $filters = array();

            if (is_numeric($search)) {
                $filters[] = "content:id:=:{$search}";
            }

            foreach ($searchContentFields as $field) {
                $filters[] = "content:{$field}:{$mode}:{$search}";
            }

            if (!empty($this->dlParams['searchTVFields'])) {
                $searchTVFields = explode(',', $this->dlParams['searchTVFields']);
                foreach ($searchTVFields as $tv) {
                    $filters[] = "tv:{$tv}:{$mode}:{$search}";
                }
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
