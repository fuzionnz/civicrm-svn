<?php

/**
 * XHTML 1.1 Text Module, defines basic text containers. Core Module.
 * @note In the normative XML Schema specification, this module
 *       is further abstracted into the following modules:
 *          - Block Phrasal (address, blockquote, pre, h1, h2, h3, h4, h5, h6)
 *          - Block Structural (div, p)
 *          - Inline Phrasal (abbr, acronym, cite, code, dfn, em, kbd, q, samp, strong, var)
 *          - Inline Structural (br, span)
 *       This module, functionally, does not distinguish between these
 *       sub-modules, but the code is internally structured to reflect
 *       these distinctions.
 */
class HTMLPurifier_HTMLModule_Text extends HTMLPurifier_HTMLModule
{
    
    public $name = 'Text';
    public $content_sets = array(
        'Flow' => 'Heading | Block | Inline'
    );
    
    public function __construct() {
        
        // Inline Phrasal -------------------------------------------------
        $this->addElement('abbr',    'Inline', 'Inline', 'Common');
        $this->addElement('acronym', 'Inline', 'Inline', 'Common');
        $this->addElement('cite',    'Inline', 'Inline', 'Common');
        $this->addElement('code',    'Inline', 'Inline', 'Common');
        $this->addElement('dfn',     'Inline', 'Inline', 'Common');
        $this->addElement('em',      'Inline', 'Inline', 'Common');
        $this->addElement('kbd',     'Inline', 'Inline', 'Common');
        $this->addElement('q',       'Inline', 'Inline', 'Common', array('cite' => 'URI'));
        $this->addElement('samp',    'Inline', 'Inline', 'Common');
        $this->addElement('strong',  'Inline', 'Inline', 'Common');
        $this->addElement('var',     'Inline', 'Inline', 'Common');
        
        // Inline Structural ----------------------------------------------
        $this->addElement('span', 'Inline', 'Inline', 'Common');
        $this->addElement('br',   'Inline', 'Empty',  'Core');
        
        // Block Phrasal --------------------------------------------------
        $this->addElement('address',     'Block', 'Inline', 'Common');
        $this->addElement('blockquote',  'Block', 'Optional: Heading | Block | List', 'Common', array('cite' => 'URI') );
        $pre = $this->addElement('pre', 'Block', 'Inline', 'Common');
        $pre->excludes = $this->makeLookup(
            'img', 'big', 'small', 'object', 'applet', 'font', 'basefont' );
        $this->addElement('h1', 'Heading', 'Inline', 'Common');
        $this->addElement('h2', 'Heading', 'Inline', 'Common');
        $this->addElement('h3', 'Heading', 'Inline', 'Common');
        $this->addElement('h4', 'Heading', 'Inline', 'Common');
        $this->addElement('h5', 'Heading', 'Inline', 'Common');
        $this->addElement('h6', 'Heading', 'Inline', 'Common');
        
        // Block Structural -----------------------------------------------
        $this->addElement('p', 'Block', 'Inline', 'Common');
        $this->addElement('div', 'Block', 'Flow', 'Common');
        
    }
    
}

