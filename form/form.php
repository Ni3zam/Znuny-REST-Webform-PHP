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
    exit(1);
}
$SessionID = $response->body->SessionID;



/**
 * TicketCreate
 *
 * 
 **/
$Title = $_POST['Title'];
$CustomerUser = $_POST['CustomerUser'];
$Queue = $_POST['Queue'];
$ArticleTitle = $_POST['ArticleTitle'];
$ArticleField = $_POST['ArticleField'];
$Priority = $_POST['Priority'];
// Encode the data as a JSON object
$body = json_encode([
    'SessionID' => $SessionID,
    'Ticket' => [
        'Title' => $Title,
        'Queue' => $Queue,
        'CustomerUser' => $CustomerUser,
        'State' => 'new',
        'Priority' => $Priority,
        'OwnerID' => 1,
    ],
    'Article' => [
        'CommunicationChannel' => 'Phone',
        'ArticleTypeID' => 1,
        'SenderTypeID' => 1,
        'Subject' => $ArticleTitle,
        'Body' => $ArticleField,
        'ContentType' => 'text/plain; charset=utf8',
        'Charset' => 'utf8',
        'MimeType' => 'text/plain',
        'From' => $CustomerUser,
    ]
]);


$response = Request::post($BaseURL."/Ticket", $headers, $body);
if ($response->body && property_exists($response->body, 'Error')) {
    $ErrorCode = $response->body->Error->ErrorCode;
    $ErrorMessage = $response->body->Error->ErrorMessage;
    exit(1);
}
$TicketNumber = $response->body->TicketNumber;
$TicketID = $response->body->TicketID;
$ArticleID = $response->body->ArticleID;



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
        exit(1);
}
$TicketData = $response->body->Ticket[0];
foreach($TicketData as $key => $value) {
    if ($value) {
        $Data = "$key: $value";
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
    exit(1);
}

?>
