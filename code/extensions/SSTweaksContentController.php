<?php
/**
 * Add additional methods to controller
 *
 * @author morven
 */
class SSTweaksContentController extends Extension {

    public function SocialNav() {
        $config = SiteConfig::current_site_config();

        $vars = array(
            "Facebook"  => ($config->FacebookURL) ? $config->FacebookURL : "",
            "Foursquare"  => ($config->FoursquareURL) ? $config->FoursquareURL : "",
            "Twitter"   => ($config->TwitterURL) ? $config->TwitterURL : "",
            "GooglePlus"=> ($config->GooglePlusURL) ? $config->GooglePlusURL : "",
            "LinkdIn"   => ($config->LinkdInURL) ? $config->LinkdInURL : "",
            "YouTube"   => ($config->YouTubeURL) ? $config->YouTubeURL : "",
            "Vimeo"   => ($config->VimeoURL) ? $config->VimeoURL : "",
            "Pinterest" => ($config->PinterestURL) ? $config->PinterestURL : ""
        );

        $this->owner->extend("UpdateSocialNav", $vars);

        return $this->owner->renderWith("SocialNav", $vars);
    }

    public function SSNavigator() {
        $member = Member::currentUser();
        $items = '';
        $message = '';
        
        if(!$member && (!Permission::check('CMS_ACCESS_CMSMain') || !Permission::check('VIEW_DRAFT_CONTENT'))) {
            return false;
        }

        if(Director::isDev() || Permission::check('CMS_ACCESS_CMSMain') || Permission::check('VIEW_DRAFT_CONTENT')) {           
            if($this->owner->dataRecord) {
                Requirements::css(CMS_DIR . '/css/SilverStripeNavigator.css');
                // Requirements::javascript(FRAMEWORK_DIR . '/thirdparty/jquery/jquery.js');
                Requirements::javascript(CMS_DIR . '/javascript/SilverStripeNavigator.js');
                
                $return = $nav = SilverStripeNavigator::get_for_record($this->owner->dataRecord);
                $items = $return['items'];
                // $message = $return['message'];
            }

            if($member) {
                $firstname = Convert::raw2xml($member->FirstName);
                $surname = Convert::raw2xml($member->Surname);
                $logInMessage = _t('ContentController.LOGGEDINAS', 'Logged in as') ." {$firstname} {$surname} - <a href=\"Security/logout\">". _t('ContentController.LOGOUT', 'Log out'). "</a>";
            } else {
                $logInMessage = sprintf(
                    '%s - <a href="%s">%s</a>' ,
                    _t('ContentController.NOTLOGGEDIN', 'Not logged in') ,
                    Config::inst()->get('Security', 'login_url'),
                    _t('ContentController.LOGIN', 'Login') ."</a>"
                );
            }

            $viewPageIn = _t('ContentController.VIEWPAGEIN', 'View Page in:');


            $ssFrontAdminMenuOut = "";
            if (class_exists('SSFrontAdminController')) {
                $ssFrontAdminMenuItems = $this->owner->getFrontAdminMenuGlobal();
                $ssFrontAdminMenuOut = $this->owner->renderWith('FrontAdminMenuGlobal');
            }

            return <<<HTML
                <div id="SilverStripeNavigator">
                    <div class="holder">
                    <div id="logInStatus">
                        $logInMessage
                    </div>
                    <div id="SSFrontAdminMenu">
                        $ssFrontAdminMenuOut
                    </div>
                    <div id="switchView" class="bottomTabs">
                        $viewPageIn 
                        $items
                    </div>
                    </div>
                </div>
                $message
HTML;

        // On live sites we should still see the archived message
        } else {
            if($date = Versioned::current_archived_date()) {
                Requirements::css(CMS_DIR . '/css/SilverStripeNavigator.css');
                $dateObj = Datetime::create($date, null);
                // $dateObj->setVal($date);
                return "<div id=\"SilverStripeNavigatorMessage\">". _t('ContentController.ARCHIVEDSITEFROM') ."<br>" . $dateObj->Nice() . "</div>";
            }
        }
    }
}
