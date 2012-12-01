<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2012                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2012
 * $Id$
 *
 */

/**
 * Main page for blog dashlet
 *
 */
class CRM_Dashlet_Page_Blog extends CRM_Core_Page {

  const CHECK_TIMEOUT = 5, CACHE_DAYS = 2;

  /**
   * List blog articles as dashlet
   *
   * @access public
   */
  function run() {
    $context = CRM_Utils_Request::retrieve('context', 'String', $this, FALSE, 'dashlet');
    $this->assign('context', $context);

    $this->assign('blog', $this->_getBlog());

    return parent::run();
  }

  /**
   * Load blog articles from cache
   * Refresh cache if expired
   *
   * @return array
   *
   * @access private
   */
  private function _getBlog() {
    // Fetch data from cache
    $cache = CRM_Core_DAO::executeQuery("SELECT data, created_date FROM civicrm_cache
      WHERE group_name = 'dashboard' AND path = 'blog'");
    if ($cache->fetch()) {
      $expire = time() - (60 * 60 * 24 * self::CACHE_DAYS);
      // Refresh data after CACHE_DAYS
      if (strtotime($cache->created_date) < $expire) {
        $new_data = $this->_getFeed();
        if ($new_data) {
          return $new_data;
        }
      }
      return unserialize($cache->data);
    }
    return $this->_getFeed();
  }

  /**
   * Parse rss feed and cache results
   *
   * @return array
   *
   * @access private
   */
  private function _getFeed() {
    ini_set('default_socket_timeout', self::CHECK_TIMEOUT);
    $feed = @simplexml_load_file('http://civicrm.org/blog/feed');
    ini_restore('default_socket_timeout');
    $blog = array();
    if ($feed && !empty($feed->channel->item)) {
      foreach ($feed->channel->item as $item) {
        // Clean up
        $item = (array) $item;
        $item['description'] = strip_tags($item['description'], "<a> <p>");
        $blog[] = $item;
      }
      if ($blog) {
        CRM_Core_BAO_Cache::setItem($blog, 'dashboard', 'blog');
      }
    }
    return $blog;
  }
}
