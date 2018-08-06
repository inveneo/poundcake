<?php
/**
 * Simple helper class that leverages Poundcake's security scheme to show or not show
 * links, as appropriate.  Method calls/signatures should be equivalent to
 * CakePHP's native HtmlHelper.
 * 
 * Developed against CakePHP 2.2.3 and PHP 5.4.x.
 *
 * Copyright 2012, Inveneo, Inc. (http://www.inveneo.org)
 *
 * Licensed under GNU General Public License.
 * 
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2012, Inveneo, Inc. (http://www.inveneo.org)
 * @author        Inveneo Dev Team <info@inveneo.org>
 * @link          http://www.inveneo.org
 * @package       app.View.Helper
 * @since         PoundcakeHTMLHelper precedes Poundcake v2.2.1
 * @license       GNU General Public License
 */

class PoundcakeHTMLHelper extends AppHelper {
    
    /*
     * Other helpers this helper relies on
     */
    var $helpers = array('Html','Form','Session'); // maybe we should have extendedHtmlHelper versus just using it?
    
    // http://twitter.github.com/bootstrap/base-css.html#images
    const ICON_LIST = '<i class="icon-list-alt"></i>'; 
    //const ICON_LIST = '<i class="icon-list"></i>';  
    //const ICON_LIST = '<i class="icon-arrow-left"></i>';
    //const ICON_NEW = '<i class="icon-pencil"></i>'; 
    const ICON_NEW = '<i class="icon-plus"></i>'; 
    const ICON_EDIT = '<i class="icon-edit"></i>';
    const ICON_DELETE = '<i class="icon-minus"></i>';
    const ICON_FILE = '<i class="icon-file"></i>';
    const ICON_SETUP = '<i class="icon-wrench"></i>';
    
    const ICON_DEFAULT = '<i class="icon-circle-arrow-right"></i>';
    //const ICON_DEFAULT = '<i class="icon-play-circle"></i>';
    
    function link($title, $url) {
        return $this->linkIfAllowed( $title, $url );
    }
    
    /*
     * Returns hyperlink if permissions permit.
     */
    function linkIfAllowed($title, $url, $icon = true) {
        $role = $this->Session->read('role');
        
        // third argument is a flag to not show any icon
        $i = null;
        if ( $icon )
            $i = $this->getIcon($title);
        
        if (($role === 'edit') || ($role === 'admin') ) {
          return $i.$this->Html->link($title, $url);
        } else {
            return $i.$title;
        }
    }
    
    /*
     * Returns hyperlink if administrator
     */
    function linkIfAdmin($title, $url) {
        $i = $this->getIcon($title);
        $role = $this->Session->read('role');
        if ($role === 'admin') {
          return $i.$this->Html->link($title, $url);
        } else {
            return $i.$title;
        }
    }

   /*
    * Returns postLink if permissions permit
    */
    function postLinkIfAllowed($title, $url = null, $options = array(), $confirmMessage = false, $showText = true) {
         // this should probably be in PoundcakeFormHelper ;-)
        $i = $this->getIcon($title);
        $role = $this->Session->read('role');
        
        // if we're not showing any text on this link, let's use the image in
        // lieu of it and make the image itself clickable
        // see basically this technique:
        // http://stackoverflow.com/questions/8105450/cakephp-formhelper-postlink-image
        if ( !$showText ) {
            $title = $i;
        }
        
        if (($showText) && (($role === 'edit') || ($role === 'admin'))) {
            return $i.$this->Form->postLink($title,$url,$options,$confirmMessage);
        } elseif ((!$showText) && (($role === 'edit') || ($role === 'admin'))) {
            // as from the article above, we need to add an escape=>false to the
            // options array so the resulting HTML is escaped properly
            // at the moment the only place where this is used is in delete of
            // an IP Space            
            $more_options = array('escape' => false );
            //$more_options = array();
            $options = array_merge( $options, $more_options );
            return $this->Form->postLink($title,$url,$options,$confirmMessage);
        }
        else {
            return $i.$title;
        }
    }
    
   /*
    * Returns postLink if administrator
    */
    function postLinkIfAdmin($title, $url = null, $options = array(), $confirmMessage = false, $showText = true) {
        $i = $this->getIcon($title);
        $role = $this->Session->read('role');
        if ( $role === 'admin') {
            return $i.$this->Form->postLink($title,$url,$options,$confirmMessage);
           
        } else {
            return $i.$title;
        }
    }
    
    function postLinkAltMessage($title, $url = null, $options = array(), $confirmMessage = false, $alternateString) {
        $i = $this->getIcon($title);
        $role = $this->Session->read('role');
        if (($role === 'edit') || ($role === 'admin') ) {
            return $i.$this->Form->postLink($title,$url,$options,$confirmMessage);
        } else {
            return $i.$title;
        }
    }
    
    private function getIcon( $string ) {
        $i='';
        if (stripos(trim($string), "New") === 0)
           $i = self::ICON_NEW;
        elseif (stripos(trim($string), "Edit") === 0)
            $i = self::ICON_EDIT;
        elseif (stripos(trim($string), "Delete") === 0)
            $i = self::ICON_DELETE;
        elseif (stripos(trim($string), "List") === 0)
            $i = self::ICON_LIST;
        
        // File -related
        elseif (stripos(trim($string), "Equipment") === 0)
            $i = self::ICON_FILE;
        elseif (stripos(trim($string), "Work") === 0)
            $i = self::ICON_FILE;
        elseif (stripos(trim($string), "KML") === 0)
            $i = self::ICON_FILE;
         
        elseif (stripos(trim($string), "Setup") === 0)
            $i = self::ICON_SETUP;
        
        else
            $i = self::ICON_DEFAULT;
        return $i;//.'&nbsp;';
    }
}


?>
