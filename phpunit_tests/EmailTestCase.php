<?php
class EmailTestCase extends PHPUnit_Framework_TestCase {

    /**
     * @var \Guzzle\Http\Client
     */
    private $mailcatcher;

    public function setUp()
    {
        $this->mailcatcher = new \Guzzle\Http\Client('http://hirdeto.net:1080');
        //$this->cleanMessages();
    }

    function testEmailReminderIsSent()
    {
        // ... trigger notifications

        $email = $this->getLastMessage();
        $this->assertEmailSenderEquals('<noreply@hirdeto.net>', $email);
        $this->assertEmailRecipientsContain('<szilard@szilard.co.uk>', $email);
        $this->assertEmailSubjectEquals('Jelszó emlékeztető', $email);
        //$this->assertEmailSubjectContains('Jelszó emlékeztető', $email);
        $this->assertEmailTextContains('A bejelentkezéshez használja az email címét valamint a következő jelszót:', $email);
        //$this->assertEmailHtmlContains('A bejelentkezéshez használja az email címét valamint a következő jelszót:', $email);

        $this->cleanMessages();
    }
    
    // api calls
    public function cleanMessages()
    {
        $this->mailcatcher->delete('/messages')->send();
    }

    public function getLastMessage()
    {
        $messages = $this->getMessages();
        if (empty($messages)) {
            $this->fail("No messages received");
        }
        // messages are in descending order
        return reset($messages);
    }

    public function getMessages()
    {
        $jsonResponse = $this->mailcatcher->get('/messages')->send();
        return json_decode($jsonResponse->getBody());
    }

    // assertions
    public function assertEmailIsSent($description = '')
    {
        $this->assertNotEmpty($this->getMessages(), $description);
    }
    
    public function assertEmailSubjectContains($needle, $email, $description = '')
    {
        $this->assertContains($needle, $email->subject, $description);
    }

    public function assertEmailSubjectEquals($expected, $email, $description = '')
    {
        $this->assertContains($expected, $email->subject, $description);
    }

    public function assertEmailHtmlContains($needle, $email, $description = '')
    {
        $response = $this->mailcatcher->get("/messages/{$email->id}.html")->send();
        $this->assertContains($needle, (string)$response->getBody(), $description);
    }

    public function assertEmailTextContains($needle, $email, $description = '')
    {
        $response = $this->mailcatcher->get("/messages/{$email->id}.plain")->send();
        $this->assertContains($needle, (string)$response->getBody(), $description);
    }

    public function assertEmailSenderEquals($expected, $email, $description = '')
    {
        $response = $this->mailcatcher->get("/messages/{$email->id}.json")->send();
        $email = json_decode($response->getBody());
        $this->assertEquals($expected, $email->sender, $description);
    }

    public function assertEmailRecipientsContain($needle, $email, $description = '')
    {
        $response = $this->mailcatcher->get("/messages/{$email->id}.json")->send();
        $email = json_decode($response->getBody());
        $this->assertContains($needle, $email->recipients, $description);
    }

}
