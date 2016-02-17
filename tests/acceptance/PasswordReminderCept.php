<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Send password reminder');

// Cleared old emails from MailCatcher
// https://github.com/captbaritone/codeception-mailcatcher-module
$I->resetEmails();

$I->amOnPage('/#/jelszo');
$I->see('Regisztrált email cim');

$I->fillField('email', 'szilard@szilard.co.uk');
$I->click('form[name=passwordForm] > input[type=submit]');

// all webriver methods:
// http://codeception.com/docs/modules/WebDriver
$I->wait(10);
//$I->waitForElement('.alert-info', 10);

$I->see("Hamarosan küldünk Önnek egy jelszó");

// https://github.com/captbaritone/codeception-mailcatcher-module
$I->seeInLastEmail("Jelszó emlékeztető");

/*
$I->amOnPage('/#/register');
$I->fillField('nev', 'davert');
$I->fillField('email', 'qwerty@szilard.co.uk');
$I->fillField('password', 'test123');
$I->fillField('confirm_password', 'test123');
$I->click('Elküldés');
$I->see('A regisztrációját rögzítettük!');
$I->see('Regisztráció');
$I->see('Név');
*/
?>
