# Generated by MaxQ [com.bitmechanic.maxq.generator.JythonCodeGenerator]
from PyHttpTestCase import PyHttpTestCase
from com.bitmechanic.maxq import Config
from com.bitmechanic.maxq import DBUtil
import commonConst, commonAPI
global validatorPkg
if __name__ == 'main':
    validatorPkg = Config.getValidatorPkgName()
# Determine the validator for this testcase.
exec 'from '+validatorPkg+' import Validator'


# definition of test class
class testSearchAll(PyHttpTestCase):
    def setUp(self):
        global db
        db = commonAPI.dbStart()
    
    def tearDown(self):
        commonAPI.dbStop(db)
    
    def runTest(self):
        self.msg('Test started')

        drupal_path = commonConst.DRUPAL_PATH

        commonAPI.login(self)

        params = [
            ('''reset''', '''1'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/search?reset=1''') % drupal_path)
        url = "%s/civicrm/contact/search" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 5 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        # self.msg("Testing URL: %s" % self.replaceURL('''http://localhost/favicon.ico'''))
        # url = "http://localhost/favicon.ico"
        # params = None
        # Validator.validateRequest(self, self.getMethod(), "get", url, params)
        # self.get(url, params)
        # self.msg("Response code: %s" % self.getResponseCode())
        # self.assertEquals("Assert number 6 failed", 404, self.getResponseCode())
        # Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''_qf_default''', '''Search:refresh'''),
            ('''contact_type''', ''''''),
            ('''group''', ''''''),
            ('''tag''', ''''''),
            ('''sort_name''', ''''''),
            ('''_qf_Search_refresh''', '''Search'''),]
        url = "%s/civicrm/contact/search" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "post", url, params)
        self.post(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 7 failed", 302, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        # self.msg("Testing URL: %s" % self.replaceURL('''http://localhost/favicon.ico'''))
        # url = "http://localhost/favicon.ico"
        # params = None
        # Validator.validateRequest(self, self.getMethod(), "get", url, params)
        # self.get(url, params)
        # self.msg("Response code: %s" % self.getResponseCode())
        # self.assertEquals("Assert number 8 failed", 404, self.getResponseCode())
        # Validator.validateResponse(self, self.getMethod(), url, params)
        
        name    = '%s' % params[4][1]
        contact = '%s' % params[1][1]
        group   = '%s' % params[2][1]
        tag     = '%s' % params[3][1]
               
        query   = 'SELECT count(DISTINCT civicrm_contact.id) FROM civicrm_contact \
        LEFT JOIN civicrm_location ON (civicrm_location.entity_table = \'civicrm_contact\' AND civicrm_contact.id = civicrm_location.entity_id AND civicrm_location.is_primary = 1) \
        LEFT JOIN civicrm_address ON civicrm_location.id = civicrm_address.location_id \
        LEFT JOIN civicrm_phone ON (civicrm_location.id = civicrm_phone.location_id AND civicrm_phone.is_primary = 1) \
        LEFT JOIN civicrm_email ON (civicrm_location.id = civicrm_email.location_id AND civicrm_email.is_primary = 1) \
        LEFT JOIN civicrm_state_province ON civicrm_address.state_province_id = civicrm_state_province.id \
        LEFT JOIN civicrm_country ON civicrm_address.country_id = civicrm_country.id \
        LEFT JOIN civicrm_group_contact ON civicrm_contact.id = civicrm_group_contact.contact_id '
        #WHERE (civicrm_contact.sort_name LIKE \'%%%s%%\') AND civicrm_contact.contact_type=\'%s\' AND  1  % (name, contact)
        
        noOfContact = db.loadVal(query)
        if noOfContact == '1' :
            string = "Found %s contact" % noOfContact
        else :
            string = "Found %s contacts" % noOfContact
        
        params = [
            ('''_qf_Search_display''', '''true'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/drupal/civicrm/contact/search?_qf_Search_display=true''') % drupal_path)
        url = "%s/civicrm/contact/search" % drupal_path
        self.msg("Testing URL %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 9 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

        print ("*********************************************************************************")
        print ("The Citeria for search is ")
        self.msg ("%s : %s" % ("Contact Type", contact))
        self.msg ("%s : %s" % ("Group       ", group))
        self.msg ("%s : %s" % ("Tag         ", tag))
        self.msg ("%s : %s" % ("Sort Name   ", name))
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

        commonAPI.logout(self)
        self.msg('Test successfully complete.')
    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testSearchAll("testSearchAll")
    test.Run()
