<?php
/**
 * Add additional methods to controller
 *
 * @author morven
 */
class SSTweaksController extends Extension {

    /**
     * Set a flash message that will appear in your templates
     *
     * The site styling currently accepts the following message types
     *
     * - success: Rendered green
     * - error: Rendered red
     * - info: Rendered blue
     *
     * @param $type Type of message
     * @param $message message to send
     * @return Controller
     */
    public function setFlashMessage($type, $message) {
        Session::set('Site.Message', array(
            'Type' => $type,
            'Message' => $message
        ));

        return $this;
    }

    /**
     * Get a flash message that is rendered into a template
     *
     * @return String
     */
    public function getFlashMessage() {
        if($message = Session::get('Site.Message')){
            Session::clear('Site.Message');
            $array = new ArrayData($message);
            return $array->renderWith('FlashMessage');
        }
    }
}
