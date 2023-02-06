<?php
declare(strict_types=1);
require_once 'vendor/autoload.php';

use Unirest\Request;

Request::defaultHeader("Accept", "application/json");
Request::defaultHeader("Content-Type", "application/json");
Request::verifyPeer(true);

$FQDN = 'FIXME';
$WebServiceName = 'GenericTicketConnectorREST';
$BaseURL = "https://$FQDN/otrs/nph-genericinterface.pl/Webservice/$WebServiceName";
$headers = [];
$body = json_encode(
    [
        "UserLogin" => "FIXME",
        "Password"  => "FIXME",
    ]
);



/**
 * SessionCreate (Create a session)
 *
 * http://doc.otrs.com/doc/api/otrs/6.0/Perl/Kernel/GenericInterface/Operation/Session/SessionCreate.pm.html
 */
$response = Request::post($BaseURL."/Session", $headers, $body);
if (!$response->body||!property_exists($response->body,'SessionID')) {
    print "No SessionID were received. \n<br>";
    exit(1);
}
$SessionID = $response->body->SessionID;
print "\nNotice: \n"<br>;
print "SessionID obtained. Your SessionID is $SessionID\n<br><br>";



/**
 * TicketCreate
 *
 * https://doc.otrs.com/doc/api/otrs/6.0/Perl/Kernel/GenericInterface/Operation/Ticket/TicketCreate.pm.html
 */
$Title= $_POST['Title'];
$CustomerUser= $_POST['CustomerUser'];
$Queue= $_POST['Queue'];
$ArticleTitle= $_POST['ArticleTitle'];
$ArticleField= $_POST['ArticleField'];
echo ("Your ticket has been sent.\n<br>");
$body = json_encode([
        'SessionID' => $SessionID,
        'Ticket' => [
            'Title' => $Title,
            'Queue' => $Queue,
            'CustomerUser' => $CustomerUser,
            'State' => 'new',
            'Priority' => '3 normal',
            'OwnerID' => 1,
        ],
        'Article' =>[
            'CommunicationChannel' => 'Email',
            'ArticleTypeID' => 1,
            'SenderTypeID' => 1,
            'Subject' => $ArticleTitle,
            'Body' => $ArticleField,
            'ContentType' => 'text/plain; charset=utf8',
            'Charset' => 'utf8',
            'MimeType' => 'text/plain',
            'From' => $CustomerUser,
        ],
    ]
);

$response = Request::post($BaseURL."/Ticket", $headers, $body);
if ($response->body && property_exists($response->body, 'Error')) {
    $ErrorCode = $response->body->Error->ErrorCode;
    $ErrorMessage = $response->body->Error->ErrorMessage;
    print "\n\n";
    print "ErrorCode $ErrorCode\n\n<br>";
    print "ErrorMessage $ErrorMessage\n\n<br>";
    print "\n\n";
    exit(1);
}
$TicketNumber = $response->body->TicketNumber;
$TicketID = $response->body->TicketID;
$ArticleID = $response->body->ArticleID;
print "<br>\nNotice: \n<br>";
print "\nThe ticket $TicketNumber was created. Check it via https://$FQDN/otrs/index.pl?Action=AgentTicketZoom;TicketID=$TicketID\n\n<br>br>";



/**
 * TicketGet
 *
 * http://doc.otrs.com/doc/api/otrs/6.0/Perl/Kernel/GenericInterface/Operation/Ticket/TicketGet.pm.html
 */
$param = [
    'SessionID' => $SessionID,
];
$response = Unirest\Request::get($BaseURL."/Ticket/${TicketID}?Extended=1", $headers, $param);
if ($response->body && property_exists($response->body, 'Error')) {
        $ErrorCode = $response->body->Error->ErrorCode;
        $ErrorMessage = $response->body->Error->ErrorMessage;
        print "\n\n";
        print "ErrorCode $ErrorCode\n\n<br>";
        print "ErrorMessage $ErrorMessage\n\n<br>";
        print "\n\n";
        exit(1);
}
$TicketData = $response->body->Ticket[0];
print "<br>\nTicket Details:\n<br>";
foreach($TicketData as $key => $value) {
    if ($value) {
        print "$key: $value\n<br>";
    }
}



/**
*
* SessionDestroy (Used to log out from Webservice account.)
*
*/
$param = [
'SessionID' => $SessionID,
];
$response = Unirest\Request::delete($BaseURL."/Session", $headers, $param);
if ($response->body && property_exists($response->body, 'Error')) {
    $ErrorCode = $response->body->Error->ErrorCode;
    $ErrorMessage = $response->body->Error->ErrorMessage;
    print "\n\n";
    print "ErrorCode $ErrorCode\n\n<br>";
    print "ErrorMessage $ErrorMessage\n\n<br>";
    print "\n\n";
    exit(1);
}
print "\nNotice: \n";
print "<br>\nSessionID $SessionID is finished.\n\n<br>";

?>
