var assert = require('assert'),
	webdriver = require('selenium-webdriver'),
    By = require('selenium-webdriver').By,
    until = require('selenium-webdriver').until;

var driver = new webdriver.Builder()
    .forBrowser('firefox')
    .build();

driver.get("http://hirdetek-api.com/#/hirdetes/feladas");
//driver.getTitle().then(function(title) {
// assert.equal(title, "PHP/JS Web Developer - Szilard Szabo");
//});
driver.findElement(By.name("targy")).sendKeys("Test targy");
driver.findElement(By.name("szoveg")).sendKeys("Test szoveg");
driver.findElement(By.xpath("//select[@name='forovat']/option[@value=1]")).click()
driver.findElement(By.name("nev")).sendKeys("Teszt Elek");
driver.findElement(By.name("email")).sendKeys("szilard@szilard.co.uk");
driver.findElement(By.name("terms")).click();
driver.findElement(By.id("feladas")).click();
/*
driver.findElement({className: 'contact_success'})
.then(function(element){
	element.getAttribute('style')
	.then(function(value){
		assert.equal(value.search("block"), 9);
	});

});
*/

driver.wait(until.titleIs('lofasz'), 500000);
