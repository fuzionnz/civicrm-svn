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
class testAddLocationType(PyHttpTestCase):
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

        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/admin/locationType''') % drupal_path)
        url = "%s/civicrm/admin/locationType" % drupal_path
        self.msg("Testing URL: %s" % url)
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 5 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''action''', '''add'''),
            ('''reset''', '''1'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/admin/locationType?action=add&reset=1''') % drupal_path)
        url = "%s/civicrm/admin/locationType" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 6 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        db = DBUtil("%s" % Common.MSQLDRIVER, "jdbc:mysql://%s/%s" % (Common.HOST, Common.DBNAME), "%s" % Common.DBUSERNAME, "%s" % Common.DBPASSWORD)
        
        queryName    = 'select name from crm_location_type'          
        queryID      = 'select max(id) from crm_location_type'
        
        locationName = db.loadRows(queryName)
        locationNum  = db.loadVal(queryID)
        
        db.close()
        
        params = [
            ('''_qf_default''', '''LocationType:next'''),
            ('''name''', '''Test Location Type'''),
            ('''description''', '''This is test Location Type'''),
            ('''_qf_LocationType_next''', '''Save'''),]
        url = "%s/civicrm/admin/locationType" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "post", url, params)
        self.post(url, params)
        for i in range(int(locationNum)) :
            if locationName[i].values()[0] == params[1][1] :
                print ("**************************************************************************************")
                print ("Mobile Provider \'" + locationName[i].values()[0] + "\' already exists")
                print ("**************************************************************************************")
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 7 failed", 200, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)
                break
            else :
                continue
        else :
            self.msg("Response code: %s" % self.getResponseCode())
            self.assertEquals("Assert number 8 failed", 302, self.getResponseCode())
            Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''reset''', '''1'''),
            ('''action''', '''browse'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/admin/locationType?reset=1&action=browse''') % drupal_path)
        url = "%s/civicrm/admin/locationType" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 9 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testAddLocationType("testAddLocationType")
    test.Run()
