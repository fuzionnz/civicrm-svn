<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Contains the Pager_Sliding class
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   HTML
 * @package    Pager
 * @author     Lorenzo Alberton <l dot alberton at quipo dot it>
 * @copyright  2003-2005 Lorenzo Alberton
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id: Sliding.php,v 1.7 2005/04/01 13:04:53 quipo Exp $
 * @link       http://pear.php.net/package/Pager
 */

/**
 * require PEAR::Pager_Common base class
 */
require_once 'Pager/Common.php';

/**
 * Pager_Sliding - Generic data paging class  ("sliding window" style)
 * Usage examples can be found in the PEAR manual
 *
 * @category   HTML
 * @package    Pager
 * @author     Lorenzo Alberton <l dot alberton at quipo dot it>
 * @copyright  2003-2005 Lorenzo Alberton
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @link       http://pear.php.net/package/Pager
 */
class Pager_Sliding extends Pager_Common
{
    // {{{ Pager_Sliding()

    /**
     * Constructor
     *
     * @param mixed $options    An associative array of option names
     *                          and their values
     * @access public
     */
    function Pager_Sliding($options = array())
    {
        //set default Pager_Sliding options
        $this->_delta                 = 2;
        $this->_prevImg               = '&laquo;';
        $this->_nextImg               = '&raquo;';
        $this->_separator             = '|';
        $this->_spacesBeforeSeparator = 3;
        $this->_spacesAfterSeparator  = 3;
        $this->_curPageSpanPre        = '<b><u>';
        $this->_curPageSpanPost       = '</u></b>';

        //set custom options
        $err = $this->_setOptions($options);
        if ($err !== PAGER_OK) {
            return $this->raiseError($this->errorMessage($err), $err);
        }
        $this->_generatePageData();
        $this->_setFirstLastText();

        if ($this->_totalPages > (2 * $this->_delta + 1)) {
            $this->links .= $this->_printFirstPage();
        }

        $this->links .= $this->_getBackLink();
        $this->links .= $this->_getPageLinks();
        $this->links .= $this->_getNextLink();

        $this->linkTags .= $this->_getFirstLinkTag();
        $this->linkTags .= $this->_getPrevLinkTag();
        $this->linkTags .= $this->_getNextLinkTag();
        $this->linkTags .= $this->_getLastLinkTag();

        if ($this->_totalPages > (2 * $this->_delta + 1)) {
            $this->links .= $this->_printLastPage();
        }
    }

    // }}}
    // {{{ getPageIdByOffset()

    /**
     * "Overload" PEAR::Pager method. VOID. Not needed here...
     * @param integer $index Offset to get pageID for
     * @deprecated
     * @access public
     */
    function getPageIdByOffset($index=null) { }

    // }}}
    // {{{ getPageRangeByPageId()

    /**
     * Given a PageId, it returns the limits of the range of pages displayed.
     * While getOffsetByPageId() returns the offset of the data within the
     * current page, this method returns the offsets of the page numbers interval.
     * E.g., if you have pageId=5 and delta=2, it will return (3, 7).
     * PageID of 9 would give you (4, 8).
     * If the method is called without parameter, pageID is set to currentPage#.
     *
     * @param integer PageID to get offsets for
     * @return array  First and last offsets
     * @access public
     */
    function getPageRangeByPageId($pageid = null)
    {
        $pageid = isset($pageid) ? (int)$pageid : $this->_currentPage;
        if (!isset($this->_pageData)) {
            $this->_generatePageData();
        }
        if (isset($this->_pageData[$pageid]) || is_null($this->_itemData)) {
            if ($this->_expanded) {
                $min_surplus = ($pageid <= $this->_delta) ? ($this->_delta - $pageid + 1) : 0;
                $max_surplus = ($pageid >= ($this->_totalPages - $this->_delta)) ?
                                ($pageid - ($this->_totalPages - $this->_delta)) : 0;
            } else {
                $min_surplus = $max_surplus = 0;
            }
            return array(
                max($pageid - $this->_delta - $max_surplus, 1),
                min($pageid + $this->_delta + $min_surplus, $this->_totalPages)
            );
        }
        return array(0, 0);
    }

    // }}}
    // {{{ getLinks()

    /**
     * Returns back/next/first/last and page links,
     * both as ordered and associative array.
     *
     * @param integer $pageID Optional pageID. If specified, links
     *                for that page are provided instead of current one.
     * @return array back/pages/next/first/last/all links
     * @access public
     */
    function getLinks($pageID = null)
    {
        if ($pageID != null) {
            $_sav = $this->_currentPage;
            $this->_currentPage = $pageID;

            $this->links = '';
            if ($this->_totalPages > (2 * $this->_delta + 1)) {
                $this->links .= $this->_printFirstPage();
            }
            $this->links .= $this->_getBackLink();
            $this->links .= $this->_getPageLinks();
            $this->links .= $this->_getNextLink();
            if ($this->_totalPages > (2 * $this->_delta + 1)) {
                $this->links .= $this->_printLastPage();
            }
        }

        $back  = str_replace('&nbsp;', '', $this->_getBackLink());
        $next  = str_replace('&nbsp;', '', $this->_getNextLink());
        $pages = $this->_getPageLinks();
        $first = $this->_printFirstPage();
        $last  = $this->_printLastPage();
        $all   = $this->links;
        $linkTags = $this->linkTags;

        if ($pageID != null) {
            $this->_currentPage = $_sav;
        }

        return array(
            $back,
            $pages,
            trim($next),
            $first,
            $last,
            $all,
            $linkTags,
            'back'  => $back,
            'pages' => $pages,
            'next'  => $next,
            'first' => $first,
            'last'  => $last,
            'all'   => $all,
            'linktags' => $linkTags
        );
    }

    // }}}
    // {{{ _getPageLinks()

    /**
     * Returns pages link
     *
     * @return string Links
     * @access private
     */
    function _getPageLinks($url = '')
    {
        //legacy setting... the preferred way to set an option now
        //is adding it to the constuctor
        if (!empty($url)) {
            $this->_path = $url;
        }
        
        //If there's only one page, don't display links
        if ($this->_clearIfVoid && ($this->_totalPages < 2)) {
            return '';
        }

        $links = '';
        if ($this->_totalPages > (2 * $this->_delta + 1)) {
            if ($this->_expanded) {
                if (($this->_totalPages - $this->_delta) <= $this->_currentPage) {
                    $expansion_before = $this->_currentPage - ($this->_totalPages - $this->_delta);
                } else {
                    $expansion_before = 0;
                }
                for ($i = $this->_currentPage - $this->_delta - $expansion_before; $expansion_before; $expansion_before--, $i++) {
                    $print_separator_flag = ($i != $this->_currentPage + $this->_delta); // && ($i != $this->_totalPages - 1)
                    
                    $this->range[$i] = false;
                    $this->_linkData[$this->_urlVar] = $i;
                    $links .= $this->_renderLink($this->_altPage.' '.$i, $i)
                           . $this->_spacesBefore
                           . ($print_separator_flag ? $this->_separator.$this->_spacesAfter : '');
                }
            }

            $expansion_after = 0;
            for ($i = $this->_currentPage - $this->_delta; ($i <= $this->_currentPage + $this->_delta) && ($i <= $this->_totalPages); $i++) {
                if ($i < 1) {
                    ++$expansion_after;
                    continue;
                }

                // check when to print separator
                $print_separator_flag = (($i != $this->_currentPage + $this->_delta) && ($i != $this->_totalPages));

                if ($i == $this->_currentPage) {
                    $this->range[$i] = true;
                    $links .= $this->_curPageSpanPre . $i . $this->_curPageSpanPost;
                } else {
                    $this->range[$i] = false;
                    $this->_linkData[$this->_urlVar] = $i;
                    $links .= $this->_renderLink($this->_altPage.' '.$i, $i);
                }
                $links .= $this->_spacesBefore
                        . ($print_separator_flag ? $this->_separator.$this->_spacesAfter : '');
            }

            if ($this->_expanded && $expansion_after) {
                $links .= $this->_separator . $this->_spacesAfter;
                for ($i = $this->_currentPage + $this->_delta +1; $expansion_after; $expansion_after--, $i++) {
                    $print_separator_flag = ($expansion_after != 1);
                    $this->range[$i] = false;
                    $this->_linkData[$this->_urlVar] = $i;
                    $links .= $this->_renderLink($this->_altPage.' '.$i, $i)
                      . $this->_spacesBefore
                      . ($print_separator_flag ? $this->_separator.$this->_spacesAfter : '');
                }
            }

        } else {
            //if $this->_totalPages <= (2*Delta+1) show them all
            for ($i=1; $i<=$this->_totalPages; $i++) {
                if ($i != $this->_currentPage) {
                    $this->range[$i] = false;
                    $this->_linkData[$this->_urlVar] = $i;
                    $links .= $this->_renderLink($this->_altPage.' '.$i, $i);
                } else {
                    $this->range[$i] = true;
                    $links .= $this->_curPageSpanPre . $i . $this->_curPageSpanPost;
                }
                $links .= $this->_spacesBefore
                       . (($i != $this->_totalPages) ? $this->_separator.$this->_spacesAfter : '');
            }
        }
        return $links;
    }

    // }}}
}
?>