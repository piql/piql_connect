module.exports = {
    'Step One: Login into piql_connect' : function(browser) {
        browser
          .url('http://localhost/')
          .assert.urlContains('https://auth.piqlconnect.com')
          .waitForElementVisible('#kc-page-title')
          .assert.containsText('#kc-page-title','Log In')
          .assert.visible('input[name=username]')
          .setValue('input[name=username]', 'kare')
          .assert.visible('input[type=password]')
          .setValue('input[type=password]', 'kare')
          .assert.visible('input[type=submit]')
          .click('input[type=submit]')
          .waitForElementVisible('.titleText')
          .assert.containsText('.titleText','DASHBOARD')
      },
    'Step Two: Access Tests': function (browser){
        browser
        .url('http://localhost/access/browse')
        .waitForElementVisible('.titleText')
        .assert.containsText('.titleText','BROWSE')
        .waitForElementVisible('.openFiles')
        .click('.openFiles')
        .waitForElementVisible('.titleText')
        .assert.containsText('.titleText','PACKAGE CONTENTS')
        .pause(2000)
        .waitForElementVisible('.subTitleLink')
        .click('.subTitleLink')
        .waitForElementVisible('.titleText')
        .assert.containsText('.titleText','BROWSE')
        .assert.visible('input[id=searchContents]')
        .clearValue('input[id=searchContents]')
        .setValue('input[id=searchContents]','test')
        .waitForElementVisible('#archivePicker')
        .end();
    }
}