<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
* Handle the batch process for one/multiple php files to one tar bzip2 file
*
* PHP version 5
*
* LICENSE: This source file is subject to version 3.0 of the PHP license
* that is available through the world-wide-web at the following URI:
* http://www.php.net/license/3_0.txt.  If you did not receive a copy of
* the PHP License and are unable to obtain it through the web, please
* send a note to license@php.net so we can mail you a copy immediately.
* @category   PHP
* @package PHP_Beautifier
* @subpackage Batch
* @author Claudio Bustos <clbustos@dotgeek.org>
* @copyright  2004-2005 Claudio Bustos
* @link     http://pear.php.net/package/PHP_Beautifier
* @link     http://clbustos.dotgeek.org
* @license    http://www.php.net/license/3_0.txt  PHP License 3.0
* @version    CVS: $Id:$
*/
/**
* Include PHP_Beautifier_Batch_FilesGz
*/
require_once 'FilesTar.php';
/**
* Handle the batch process for one/multiple php files to one tar bzip2 file
*
* @category   PHP
* @package PHP_Beautifier
* @author Claudio Bustos <clbustos@dotgeek.org>
* @copyright  2004-2005 Claudio Bustos
* @link     http://pear.php.net/package/PHP_Beautifier
* @link     http://clbustos.dotgeek.org
* @license    http://www.php.net/license/3_0.txt  PHP License 3.0
* @version    Release: 0.1.7
*/
class PHP_Beautifier_Batch_Output_FilesBz2 extends PHP_Beautifier_Batch_Output_FilesTar {
    protected $sCompress = 'bz2';
    protected $sExt = 'tar.bz2';
    
}
?>