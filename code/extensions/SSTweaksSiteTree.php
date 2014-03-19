<?php
/**
 * Add additional variables to default siteconfig
 *
 * @author morven
 */
class SSTweaksSiteTree extends DataExtension {

    private static $db = array(
        "SummaryContent" => "Text",
		"ShowChildren" => "Boolean",
		// "ShowContact" => "Boolean",
        "ShowInTopMenus" => "Boolean",
        "ShowInMainMenus" => "Boolean",
        "ShowInFooterMenus" => "Boolean"
    );
    
    private static $has_one = array(
        "SummaryImage" => "Image"
    );

    function updateCMSFields(FieldList $fields) {
        $summary_fields = ToggleCompositeField::create('Summary', 'Summary Info',
			array(
				TextareaField::create("SummaryContent", $this->owner->fieldLabel('SummaryContent')),
				UploadField::create("SummaryImage", $this->owner->fieldLabel('SummaryImage'))
			)
		)->setHeadingLevel(4);
        
        $fields->addFieldToTab('Root.Main', $summary_fields, 'Metadata');
    }

    function updateSettingsFields(FieldList $fields) {
        $children = FieldGroup::create(
	        CheckboxField::create('ShowChildren', 'Show children in the content area?')
		)->setTitle('Children of this page');
		
	    $fields->addFieldToTab('Root.Settings', $children);
	    
		// $contact = FieldGroup::create(
		// 	CheckboxField::create('ShowContact', 'Show contact info on this page?')
		// )->setTitle('Contact Info (from settings)');
		
		// $fields->addFieldToTab('Root.Settings', $contact);

        $fieldGroup = new FieldGroup(
            new CheckboxField("ShowInTopMenus", "Top menu"), new CheckboxField("ShowInMainMenus", "Main menu"), new CheckboxField("ShowInFooterMenus", "Footer menu")
        );

        $fields->addFieldToTab("Root.Settings", $fieldGroup->setTitle('Where page will be shown?'));
    }

	
	public function SortedChildren($num = 30) {
    	$id = $this->owner->ID;
        return DataObject::get("SiteTree", "ParentID = $id", "Created DESC", "", $num);
    }

    public function UnsortedChildren($num = 30) {
    	$id = $this->owner->ID;
        return DataObject::get("SiteTree", "ParentID = $id", "", "", $num);
    }

    public function UnsortedSiblings($num = 30) {
    	$parent = $this->owner->ParentID;
        return DataObject::get("SiteTree", "ParentID = $parent", "", "", $num);
    }

    public function SortedSiblings($num = 30) {
    	$parent = $this->owner->ParentID;
        return DataObject::get("SiteTree", "ParentID = $parent", "Created DESC", "", $num);
    }

    
    public function PageById($id){
        return SiteTree::get()->byID((int)$id);
    }

    public function GeneralMenu() {
        return DataObject::get("SiteTree", "ShowInTopMenus != 1 AND ShowInFooterMenus != 1 AND ShowInMenus = 1 AND ParentID = 0");
    }

    public function MainMenu() {
        return DataObject::get("SiteTree", "ShowInMainMenus = 1 AND ShowInMenus = 1 AND ParentID = 0");
    }

    public function TopMenu() {
        return DataObject::get("SiteTree", "ShowInTopMenus = 1 AND ShowInMenus = 1 AND ParentID = 0");
    }

    public function FooterMenu() {
        return DataObject::get("SiteTree", "ShowInFooterMenus = 1 AND ShowInMenus = 1 AND ParentID = 0");
    }

    public function PrevNextPage($mode = 'next') {
    	$parent = $this->owner->ParentID;
    	$sort = $this->owner->Sort;

        if ($mode == 'next') {
            $where = "ParentID = ($parent) AND Sort > ($sort)";
            $sort = "Sort ASC";
        } elseif ($mode == 'prev') {
            $where = "ParentID = ($parent) AND Sort < ($sort)";
            $sort = "Sort DESC";
        } else {
            return false;
        }

        $out = DataObject::get("SiteTree", $where, $sort, null, 1);

        if ($out) {
            return $out;
        } else {
            return false;
        }
    }


}
