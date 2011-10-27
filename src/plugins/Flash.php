<?php

class FlashPlugin
{
    public static function start()
    {
        Atomik::registerHelper('flash', 'FlashPlugin::flash');
        Atomik::registerHelper('flashMessages', 'FlashPlugin::renderFlashMessages');
        Atomik::registerSelector('flash', 'FlashPlugin::getFlashMessages');
    }
    
    /**
     * Saves a message that can be retrieve only once
     * 
     * @param string|array $message One message as a string or many messages as an array
     * @param string $label
     */
    public static function flash($message, $label = 'default')
    {
        if (!isset($_SESSION)) {
            throw new Atomik_Exception('The session must be started before using Atomik::flash()');
        }
        
        Atomik::fireEvent('Atomik::Flash', array(&$message, &$label));
        
        if (!Atomik::has('session/__FLASH/' . $label)) {
            Atomik::set('session/__FLASH/' . $label, array());
        }
        Atomik::add('session/__FLASH/' . $label, $message);
    }
    
    /**
     * Returns the flash messages saved in the session
     * 
     * @internal 
     * @param string $label Whether to only retreives messages from this label. When null or 'all', returns all messages
     * @param bool $delete Whether to delete messages once retrieved
     * @return array An array of messages if the label is specified or an array of array message
     */
    public static function getFlashMessages($label = 'all', $delete = true) {
        if (!Atomik::has('session/__FLASH')) {
            return array();
        }
        
        if (empty($label) || $label == 'all') {
        	if ($delete) {
            	return Atomik::delete('session/__FLASH');
        	}
        	return Atomik::get('session/__FLASH');
        }
        
        if (!Atomik::has('session/__FLASH/' . $label)) {
            return array();
        }
        
        if ($delete) {
        	return Atomik::delete('session/__FLASH/' . $label);
        }
        return Atomik::get('session/__FLASH/' . $label);
    }
    
    public static function renderFlashMessages($id = 'flash-messages')
    {
        $html = '';
    	foreach (self::getFlashMessages() as $label => $messages) {
    	    foreach ($messages as $message) {
    	        $html .= sprintf('<li class="%s">%s</li>', $label, $message);
    	    }
    	}
    	if (empty($html)) {
    	    return '';
    	}
    	return '<ul id="' . $id . '">' . $html . '</ul>';
    }
}
