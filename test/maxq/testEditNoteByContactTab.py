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
class testEditNoteByContactTab(PyHttpTestCase):
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
            ('''_qf_default''', '''Search:refresh'''),
            ('''contact_type''', ''''''),
            ('''group''', ''''''),
            ('''tag''', ''''''),
            ('''sort_name''', ''''''),
            ('''_qf_Search_refresh''', '''Search'''),]
        url = "%s/civicrm/contact/search/basic" % drupal_path
        Validator.validateRequest(self, self.getMethod(), "post", url, params)
        self.post(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 7 failed", 302, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''_qf_Search_display''', '''true'''),]
        url = "%s/civicrm/contact/search/basic" % drupal_path
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 8 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''set''', '''1'''),
            ('''path''', '''civicrm/server/search'''),]
        url = "%s/civicrm/server/search" % drupal_path
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 9 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''q''', '''civicrm/contact/search/basic'''),
            ('''force''', '''1'''),
            ('''sortByCharacter''', '''Z'''),]
        url = "%s/civicrm/contact/search/basic" % drupal_path
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 10 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''set''', '''1'''),
            ('''path''', '''civicrm/server/search'''),]
        url = "%s/civicrm/server/searc" % drupal_path
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 11 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        name    = 'Zope, Manish'
        queryID = 'select id from civicrm_contact where sort_name=\'%s\'' % name
        
        contactID = db.loadVal(queryID)
        if contactID :
            CID = '''%s''' % contactID
            
            note      = 'This is Test Note From Contact Tab'
            queryID   = 'select id from civicrm_note where note like \'%%%s%%\'' % note
            noteID    = db.loadVal(queryID)
            
            if noteID :
                NID = '''%s''' % noteID
                
                params = [
                    ('''reset''', '''1'''),
                    ('''cid''', CID),]
                url = "%s/civicrm/contact/view" % drupal_path
                self.msg("Testing URL: %s" % url)
                Validator.validateRequest(self, self.getMethod(), "get", url, params)
                self.get(url, params)
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 12 failed", 200, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)
                
                params = [
                    ('''id''', NID),
                    ('''action''', '''update'''),
                    ('''cid''', CID),]
                url = "%s/civicrm/contact/view/note" % drupal_path
                Validator.validateRequest(self, self.getMethod(), "get", url, params)
                self.get(url, params)
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 13 failed", 200, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)
                
                params = [
                    ('''_qf_default''', '''Note:next'''),
                    ('''note''', '''This is Test Note from Contact Tab...Doing test for Editing the Note'''),
                    ('''_qf_Note_next''', '''Save'''),]
                url = "%s/civicrm/contact/view/note" % drupal_path
                self.msg("Testing URL: %s" % url)
                Validator.validateRequest(self, self.getMethod(), "post", url, params)
                self.post(url, params)
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 14 failed", 302, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)
                
                editNote = params[1][1]
                params = [
                    ('''action''', '''browse'''),]
                url = "%s/civicrm/contact/view/note" % drupal_path
                self.msg("Testing URL: %s" % url)
                Validator.validateRequest(self, self.getMethod(), "get", url, params)
                self.get(url, params)
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 15 failed", 200, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)
                
                if self.responseContains('%s' % editNote) :
                    print ("**************************************************************************************")
                    print "The Note \'%s\' is Edited Successfully" % note
                    print ("**************************************************************************************")
                else :
                    print ("**************************************************************************************")
                    print "Some Problem while Editing \'%s\' Note" % note
                    print ("**************************************************************************************")
            else :
                print ("**************************************************************************************")
                print ("There is no Note like \'%s\'") % note
                print ("**************************************************************************************")
        else :
            print "********************************************************************************"
            print "Required Contact Does not Exists"
            print "********************************************************************************"
        
        commonAPI.logout(self)
        self.msg('Test successfully complete.')
    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testEditNoteByContactTab("testEditNoteByContactTab")
    test.Run()
