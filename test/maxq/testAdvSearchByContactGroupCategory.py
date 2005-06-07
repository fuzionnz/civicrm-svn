# Generated by MaxQ [com.bitmechanic.maxq.generator.JythonCodeGenerator]
from PyHttpTestCase import PyHttpTestCase
from com.bitmechanic.maxq import Config
from com.bitmechanic.maxq import DBUtil
import Common
global validatorPkg
if __name__ == 'main':
    validatorPkg = Config.getValidatorPkgName()
# Determine the validator for this testcase.
exec 'from '+validatorPkg+' import Validator'


# definition of test class
class testAdvSearchByContactGroupCategory(PyHttpTestCase):
    def runTest(self):
        self.msg('Test started')

        drupal_path = Common.DRUPAL_PATH
        
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/''') % drupal_path)
        url = "%s/" % drupal_path
        self.msg("Testing URL: %s" % url)
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 1 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

        params = [
            ('''edit[destination]''', '''node'''),
            ('''edit[name]''', Common.USERNAME),
            ('''edit[pass]''', Common.PASSWORD),
            ('''op''', '''Log in'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/user/login?edit[destination]=node&edit[name]=manishzope&edit[pass]=manish&op=Log in''') % drupal_path)
        url = "%s/user/login" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "post", url, params)
        self.post(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 2 failed", 302, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/node''') % drupal_path)
        url = "%s/node" % drupal_path
        self.msg("Testing URL: %s" % url)
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 3 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/search''') % drupal_path)
        url = "%s/civicrm/contact/search" % drupal_path
        self.msg("Testing URL: %s" % url)
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 4 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

        self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/search/advanced''') % drupal_path)
        url = "%s/civicrm/contact/search/advanced" % drupal_path
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 4 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        # self.msg("Testing URL: %s" % self.replaceURL('''http://localhost/favicon.ico'''))
        # url = "http://localhost/favicon.ico"
        # params = None
        # Validator.validateRequest(self, self.getMethod(), "get", url, params)
        # self.get(url, params)
        # self.msg("Response code: %s" % self.getResponseCode())
        # self.assertEquals("Assert number 5 failed", 404, self.getResponseCode())
        # Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''_qf_default''', '''Advanced:refresh'''),
            ('''sort_name''', ''''''),
            ('''_qf_Advanced_refresh''', '''Search'''),
            ('''cb_contact_type[Individual]''', '''1'''),
            ('''cb_group[2]''', '''1'''),
            ('''cb_tag[4]''', '''1'''),
            ('''cb_tag[5]''', '''1'''),
            ('''street_name''', ''''''),
            ('''city''', ''''''),
            ('''state_province''', ''''''),
            ('''country''', ''''''),
            ('''postal_code''', ''''''),
            ('''postal_code_low''', ''''''),
            ('''postal_code_high''', ''''''),]
        url = "%s/civicrm/contact/search/advanced" % drupal_path
        Validator.validateRequest(self, self.getMethod(), "post", url, params)
        self.post(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 6 failed", 302, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

        db = DBUtil("%s" % Common.MSQLDRIVER, "jdbc:mysql://%s/%s" % (Common.DBHOST, Common.DBNAME), "%s" % Common.DBUSERNAME, "%s" % Common.DBPASSWORD)

        contact = 'Individual'
        group   = 'Summer Program Volunteers'
        tag1    = 'Major Donor'
        tag2    = 'Volunteer'

        query   = 'SELECT count(DISTINCT crm_contact.id)  FROM crm_contact \
        LEFT JOIN crm_location ON (crm_contact.id = crm_location.contact_id AND crm_location.is_primary = 1) \
        LEFT JOIN crm_address ON crm_location.id = crm_address.location_id \
        LEFT JOIN crm_phone ON (crm_location.id = crm_phone.location_id AND crm_phone.is_primary = 1) \
        LEFT JOIN crm_email ON (crm_location.id = crm_email.location_id AND crm_email.is_primary = 1) \
        LEFT JOIN crm_state_province ON crm_address.state_province_id = crm_state_province.id \
        LEFT JOIN crm_country ON crm_address.country_id = crm_country.id \
        LEFT JOIN crm_group_contact ON crm_contact.id = crm_group_contact.contact_id \
        LEFT JOIN crm_entity_tag ON crm_contact.id = crm_entity_tag.entity_id \
        WHERE contact_type IN ( \'%s\' ) AND group_id IN ( 2 ) AND crm_group_contact.status=\"In\" AND tag_id IN (4,5) AND 1' % contact
        
        noOfContact = db.loadVal(query)
        if noOfContact == '1' :
            string = "Found %s contact" % noOfContact
        else :
            string = "Found %s contacts" % noOfContact

        db.close()

        params = [
            ('''_qf_Advanced_display''', '''true'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/search/advanced?_qf_Advanced_display=true''') % drupal_path)
        url = "%s/civicrm/contact/search/advanced" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 7 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

        print ("*********************************************************************************")
        print ("The Citeria for search is ")
        self.msg ("%s : %s" % ("Contact ", contact))
        self.msg ("%s : %s" % ("Group   ", group))
        self.msg ("%s : %s, %s" % ("Tag     ", tag1, tag2))
        print ("*********************************************************************************")
        
        if self.responseContains(string) :
            print ("*********************************************************************************")
            self.msg ("Search \"%s\"" % string)
            print ("*********************************************************************************")
        
        elif noOfContact == '0' :
            print ("*********************************************************************************")
            self.msg("The Response is \"%s\"" % string )
            print ("*********************************************************************************")            
        
        else :
            print ("*********************************************************************************")
            self.msg("The Response does not match with the result from the database ")
            print ("*********************************************************************************")            
        

    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testAdvSearchByContactGroupCategory("testAdvSearchByContactGroupCategory")
    test.Run()
