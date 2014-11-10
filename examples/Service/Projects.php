<?php

include __DIR__.'/../../autoload.php';

/**
 * Authentication
 *
 * You'll get the client id and secret at the plaform (API Access)
 **/
$Client = new Productsup\Client();
$Client->id = 1234;
$Client->secret = 'simsalabim';

/**
 * Initialize the Projects Service where you can
 *
 * Create a new project (Projects->insert())
 * Delete a project (and all related sites) (Projects->delete())
 * Get a list of sites (Projects->get())
 */
$Projects = new Productsup\Service\Projects($Client);

/* Override the host to test on development instance */
// $Projects->host = 'local.api.productsup.com';

/**
 * Create a new project
 *
 * A new project only needs a title
 **/
$Project = new Productsup\Platform\Project();
$Project->name = "Testproject ".uniqid();

try {
    $NewProject = $Projects->insert($Project);
} catch (\Productsup\Exceptions\ServerException $e) {
    // A exception at the API Server happened, should not happen but may be caused by a short down time
    // You may want to retry it later, if you keep getting this kind of exceptions please notice us.
    throw new Exception('Error at the productsup API, retry later');
} catch (\Productsup\Exceptions\ClientException $e) {
    // Most likely some of the data you provided was malformed
    // The error codes follow http status codes, @see http://en.wikipedia.org/wiki/List_of_HTTP_status_codes#4xx_Client_Error
    // The message may give more information on what was wrong:
    echo $e->getCode().' '.$e->getMessage();
} catch (\Exception $e) {
    // Exceptions not within the Productsup namespace are not thrown by the client, so these exceptions were most likely
    // thrown from your application or another 3rd party application

    // however, if you don't catch Productsup exceptions explicitly, you can catch them all like this
    echo $e->getCode().' '.$e->getMessage();
    throw $e;
}

/**
 * Get list of projects in your account
 */
$projects = $Projects->get();
\Productsup\Utils\CommandLine::infoText('all projects belonging to the identified client: ');
foreach ($projects as $Project) {
    echo sprintf('%s: %s', $Project->id, $Project->name).PHP_EOL;
}

/**
 * Create a new project
 *
 * A new project only needs a title
 **/
$Project = new Productsup\Platform\Project();
$Project->name = "Testproject ".uniqid();

$NewProject = $Projects->insert($Project);
\Productsup\Utils\CommandLine::infoText('inserted a new project, and got as result: ');
print_r($NewProject);

$insertedId = $NewProject->id; // remembering the id, so we can use it for other operations

\Productsup\Utils\CommandLine::infoText('id of the new project: ');
print_r($insertedId);
echo PHP_EOL;
/**
 * get one project, identified by its id:
 */
$getResonse = $Projects->get($insertedId);
\Productsup\Utils\CommandLine::infoText('Got this as a result for the get Request: ');
print_r($getResonse);

/**
 * to update the project, you can change the properties of the object and send it via the update function
 */
$receivedProject = $getResonse[0];
$receivedProject->name = 'updated projectname';

$updatedProject = $Projects->update($receivedProject);
\Productsup\Utils\CommandLine::infoText('Got this as a result for the update Request: ');
print_r($updatedProject);

/**
 * if you want to delete one project, you can identify it by id, or pass the project as parameter
 */

$deleteResponse = $Projects->delete($updatedProject);
\Productsup\Utils\CommandLine::infoText('Got this as a result for the delete Request: ');
var_dump($deleteResponse);

/**
 * if you try to get the deleted object now, you will receive an error:
 */
try {
    \Productsup\Utils\CommandLine::infoText('Trying to get a deleted project: ');
    $Projects->get($updatedProject->id);
} catch(\Productsup\Exceptions\ClientException $e) {
    echo $e->getCode().' '.$e->getMessage().PHP_EOL;
}


