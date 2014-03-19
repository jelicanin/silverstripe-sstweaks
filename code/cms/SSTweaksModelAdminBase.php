<?php

class SSTweaksModelAdminBase extends ModelAdmin {
	
	/**
	 * Change this variable if you don't want the Import from CSV form to appear.
	 * This variable can be a boolean or an array.
	 * If array, you can list className you want the form to appear on. i.e. array('myClassOne','myClasstwo')
	 */
	public $showImportForm = false;

	private static $allowed_actions = array(
		"ItemEditForm"
	);

	function urlSegmenter() {
		return self::$url_segment;
	}

	function getEditForm($id = null, $fields = null) {
	    $form = parent::getEditForm($id , $fields);
	    $listfield = $form->Fields()->fieldByName($this->modelClass);
	
	    if($gridField = $listfield->getConfig()->getComponentByType('GridFieldDetailForm')) {
	        $gridField->setItemRequestClass('SSTweaksModelAdminBase_FieldDetailForm_ItemRequest');
	    }
	
	    return $form;
	}

	/**
	 * @return array Map of class name to an array of 'title' (see {@link $managed_models})
	 */
	function getManagedModels() {
		$models = EcommerceConfig::get($this->class, "managed_models");
		foreach($models as $key => $model) {
			if(is_array($model)) {
				$model = $key;
			}
			if(!class_exists($model)) {
				unset($models[$key]);
			}
		}
		Config::inst()->update('SSTweaksModelAdminBase', 'managed_models', $models);
		return parent::getManagedModels();
	}

}

class SSTweaksModelAdminBase_FieldDetailForm_ItemRequest extends GridFieldDetailForm_ItemRequest {
	
	private static $allowed_actions = array(
		'ItemEditForm'
	);

    public function ItemEditForm() {
        $form = parent::ItemEditForm();
        $formActions = $form->Actions();
        if($actions = $this->record->getCMSActions()) {
            foreach($actions as $action) {
                $formActions->push($action);
            }
        }
        $form->setActions($formActions);
        return $form;
    }

	// public function updateItemEditForm($form) {
	// // public function ItemEditForm() {
	// //     $form = parent::ItemEditForm();
	//     $formActions = $form->Actions();

	//     if($actions = $this->record->getCMSActions()) {
	//         foreach($actions as $action) {
 //        		$action->addExtraClass('ss-ui-action-constructive');
	//             $formActions->push($action);
	//         }
	//     }

	//     $form->setActions($formActions);
	//     return $form;
	// }

    // function myAction($data, $form) {

    //     //do things

    //     $form->sessionMessage('My Action has been successful', 'good');

    //     if ($this->gridField->getList()->byId($this->record->ID)) {
    //         return $this->edit(Controller::curr()->getRequest());
    //     } else {
    //         $noActionURL = Controller::curr()->removeAction($data['url']);
    //         Controller::curr()->getRequest()->addHeader('X-Pjax', 'Content');
    //         return Controller::curr()->redirect($noActionURL, 302);
    //     }
    // }

}
