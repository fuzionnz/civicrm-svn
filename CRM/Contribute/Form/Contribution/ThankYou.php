<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Contribute/Form/ContributionBase.php';

/**
 * form for thank-you / success page - 3rd step of online contribution process
 */
class CRM_Contribute_Form_Contribution_ThankYou extends CRM_Contribute_Form_ContributionBase {

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess( );

        $this->_params = $this->get( 'params' );
        $is_deductible = $this->get('is_deductible');
        $this->assign('is_deductible',$is_deductible);
        $this->assign( 'thankyou_title', $this->_values['thankyou_title'] );
        $this->assign( 'thankyou_text' , $this->_values['thankyou_text']  );
        $this->assign( 'thankyou_footer' , $this->_values['thankyou_footer']  );

        CRM_Utils_System::setTitle($this->_values['thankyou_title']);
    }

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        $this->assignToTemplate( );
        $productID    = $this->get ('productID');
        $option       = $this->get ('option');
        $membershipID = $this->get ('membershipID');

        if ( $productID ) {
            require_once 'CRM/Contribute/BAO/Premium.php';  
            CRM_Contribute_BAO_Premium::buildPremiumBlock( $this , $this->_id ,false ,$productID, $option);
        }

        if ( $membershipID ) {
            $tansactionID     = $this->get('membership_trx_id');
            $membershipAmount = $this->get('membership_amount');
            $renewalMode = $this->get("renewal_mode");
            $this->assign('membership_trx_id',$tansactionID);
            $this->assign('membership_amount',$membershipAmount);
            $this->assign('renewal_mode',$renewalMode);
            
            CRM_Member_BAO_Membership::buildMembershipBlock( $this , $this->_id ,false ,$membershipID ,true );
        }


        $this->assign( 'trxn_id', $this->_params['trxn_id'] );       
        $this->assign( 'receive_date', 
                       CRM_Utils_Date::mysqlToIso( $this->_params['receive_date'] ) );

        // can we blow away the session now to prevent hackery
        $this->controller->reset( );
    }

}

?>
