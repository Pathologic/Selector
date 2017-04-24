# Selector
custom TV to replace mm_ddSelectDocuments
Based on [Tokenize](https://www.zellerda.com/projects/jquery/tokenize)

Selector allows to choose documents from dropdown list and save their ids in TV.

You can modify it via config files and custom controllers. 

Config file contains an array with some Tokenize options:
```
<?php
return array(
    'maxElements'  => 0,
    'nbDropdownElements' => 10,
    'searchMaxLength' => 30,
    'searchMinLength' => 0
);
```

If your TV's name is "related" then config file is "assets/tvs/selector/config/related.php".

With custom controllers you can modify the dropdown list data. For TV named "related" you should create "assets/tvs/selector/lib/related.controller.class.php" file (for autoload with Selector; if you want to use arbitrary file name and location then load class file manually with OnManagerPageInit plugin) with controller class that extends \Selector\SelectorController in it:
```
<?php namespace Selector;
include_once(MODX_BASE_PATH.'assets/tvs/selector/lib/controller.class.php');
class RelatedController extends SelectorController {
    public function __construct($modx) {
        parent::__construct($modx);
        $this->dlParams['parents'] = 5;
        $this->dlParams['addWhereList'] = 'c.published = 1';
    }
}
```

Pay attention that class name is "RelatedController".

Request to the controller contains the following data in $_REQUEST:
* doc_id - document id (0 for new documents);
* doc_parent - document parent id;
* doc_template - document template id;
* tvid - TV id;
* tvname - TV name;
* search - query string.

Selector is discussed [here](http://modx.im/blog/addons/3461.html)