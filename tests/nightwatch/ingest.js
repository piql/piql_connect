module.exports = {
  '@disabled': true,
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
    "Step Two: Ingest Upload": function (browser){
        browser
        .url('http://localhost/ingest/uploader')
        .waitForElementVisible('.titleText')
        .assert.containsText('.titleText','UPLOAD')
        .assert.visible('select[id=archivePicker]')
        .assert.containsText('#archivePicker','Luftforsvarsmuseet (LMU)') //this would alsp be nice if the select could work off nightwatch
        .assert.visible('select[id=holdingPicker]')
        .assert.containsText('#holdingPicker','350000-449999') //not a permanent solution. will need something to select
        .assert.visible('input[type=text]')
        .clearValue('input[type=text]')
        .setValue('input[type=text]','test-e2e')
        .waitForElementVisible('input[type=file]')
        .setValue('input[type=file]', require('path').resolve(__dirname + '/Kampala-City.jpg'))
        .waitForElementVisible('.fileName',5000)
        .assert.containsText('.fileName','Kampala-City.jpg')
        .pause(5000)
        .assert.visible('button[id=processButton]')
        .click('button[id=processButton')
        .waitForElementVisible('#b-toaster-bottom-right',5000)
        .assert.containsText('#b-toaster-bottom-right','Ready for processing')
        .pause(5000)
    },
    "Step Three: Check Processing": function (browser){
        browser
        .url('http://localhost/ingest/processing')
        .waitForElementVisible('.titleText')
        .assert.containsText('.titleText','PROCESSING')
        .pause(5000)
        .waitForElementVisible('.upload-text')
        .assert.containsText('.upload-text','Transferring')
        .pause(15000)
        .assert.containsText('.upload-text','Building packages')
        .pause(2000, function (){ console.log('waiting 1.5 minutes until the package appears in offline storage...') })
        .pause(90000)
    },
    "Step Four: PiqlIT": function (browser) {
      browser
      .url('http://localhost/ingest/offline')
      .waitForElementVisible('.titleText')
      .assert.containsText('.titleText','OFFLINE STORAGE')
      .assert.visible('input[type=text]')
      .clearValue('input[type=text]')
      .setValue('input[type=text]','test')
      .assert.visible('button[id=processButton]')
      .click('button[id=processButton')
      .pause(5000, function (){ console.log("Proceeding to status page") })
    },
    "Step Five: Confirm Package in Status": function (browser){
      browser
      .url('http://localhost/ingest/status')
      .waitForElementVisible('.titleText')
      .assert.containsText('.titleText','STATUS FOR OFFLINE STORAGE')
      .waitForElementVisible('.test')
      .assert.containsText('.test','test')
      .pause(2000, function (){ console.log("Successful Ingest End to End Test!!!") })
      .end();

    }

  };