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
class testAddContactHousehold(PyHttpTestCase):
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
        url = "%s/civicrm/contact/search" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 5 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''c_type''', '''Household'''),
            ('''reset''', '''1'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/addH?c_type=Household&reset=1''') % drupal_path)
        url = "%s/civicrm/contact/addH" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 6 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''_qf_default''', '''Edit:next'''),
            ('''household_name''', '''Zope House'''),
            ('''nick_name''', '''Zope Villa'''),
            ('''privacy[do_not_email]''', '''1'''),
            ('''preferred_communication_method''', '''Post'''),
            ('''location[1][location_type_id]''', '''1'''),
            ('''location[1][is_primary]''', '''1'''),
            ('''location[1][phone][1][phone_type]''', '''Phone'''),
            ('''location[1][phone][1][phone]''', '''567890'''),
            ('''location[1][phone][2][phone_type]''', '''Mobile'''),
            ('''location[1][phone][2][phone]''', '''1345678090'''),
            ('''location[1][email][1][email]''', '''zh@yahoo.com'''),
            ('''location[1][email][2][email]''', '''zope_house@zope.com'''),
            ('''location[1][im][1][provider_id]''', '''4'''),
            ('''location[1][im][1][name]''', '''Nice to see u '''),
            ('''location[1][im][2][provider_id]''', ''''''),
            ('''location[1][im][2][name]''', ''''''),
            ('''location[1][address][street_address]''', '''23, jagjivandas colony, sector no 23, kothrud'''),
            ('''location[1][address][supplemental_address_1]''', ''''''),
            ('''location[1][address][supplemental_address_2]''', ''''''),
            ('''location[1][address][city]''', '''Pune'''),
            ('''location[1][address][state_province_id]''', '''1113'''),
            ('''location[1][address][postal_code]''', '''4578963'''),
            ('''location[1][address][country_id]''', '''1101'''),
            ('''location[2][location_type_id]''', '''2'''),
            ('''location[2][phone][1][phone_type]''', ''''''),
            ('''location[2][phone][1][phone]''', ''''''),
            ('''location[2][phone][2][phone_type]''', ''''''),
            ('''location[2][phone][2][phone]''', ''''''),
            ('''location[2][email][1][email]''', ''''''),
            ('''location[2][email][2][email]''', ''''''),
            ('''location[2][im][1][provider_id]''', ''''''),
            ('''location[2][im][1][name]''', ''''''),
            ('''location[2][im][2][provider_id]''', ''''''),
            ('''location[2][im][2][name]''', ''''''),
            ('''location[2][address][street_address]''', ''''''),
            ('''location[2][address][supplemental_address_1]''', ''''''),
            ('''location[2][address][supplemental_address_2]''', ''''''),
            ('''location[2][address][city]''', ''''''),
            ('''location[2][address][state_province_id]''', ''''''),
            ('''location[2][address][postal_code]''', ''''''),
            ('''location[2][address][country_id]''', ''''''),
            ('''note''', '''This is Zope House. '''),
            ('''_qf_Edit_next''', '''Save'''),]
        url = "%s/civicrm/contact/addH" % drupal_path
        self.msg("Testing URL: %s" % url)
        
        queryID = "select id from civicrm_contact where sort_name=\'%s\'" % params[1][1]
        cid     = db.loadVal(queryID)
        
        if cid :
            Validator.validateRequest(self, self.getMethod(), "post", url, params)
            self.post(url, params)
            self.msg("Response code: %s" % self.getResponseCode())
            self.assertEquals("Assert number 7 failed", 200, self.getResponseCode())
            Validator.validateResponse(self, self.getMethod(), url, params)
            print "******************************************************************"
            print "Household Contact \'%s\' already exists" % params[1][1]
            print "******************************************************************"
        else :
            Validator.validateRequest(self, self.getMethod(), "post", url, params)
            self.post(url, params)
            self.msg("Response code: %s" % self.getResponseCode())
            self.assertEquals("Assert number 8 failed", 302, self.getResponseCode())
            Validator.validateResponse(self, self.getMethod(), url, params)
            print "******************************************************************"
            print "Household Contact \'%s\' Added Successfully" % params[1][1]
            print "******************************************************************"
            cid     = db.loadVal(queryID)
                
        CID = '''%s''' % cid
        params = [
            ('''reset''', '''1'''),
            ('''cid''', CID),]
        url = "%s/civicrm/contact/view" % drupal_path 
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 9 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        commonAPI.logout(self)
        self.msg("Test successfully complete")
    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testAddContactHousehold("testAddContactHousehold")
    test.Run()

