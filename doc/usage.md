# Usage

## Get your creadentials

As you have read in the README, the library allows you to request the google analytics service without user interaction.
In order to make it possible, you need to create a Google Service Account. Here, the explanation:

 * Create a [Google App](http://code.google.com/apis/console).
 * Enable the Google Analytics service.
 * Create a service account on [Google App](http://code.google.com/apis/console) (Tab "API Access", choose
   "Create client ID" and then "Service account").
 * You should have received the `client_id` and `profile_id` in a email from Google but if you don't, then:
   * Check the "API Access" tab of your [Google App](http://code.google.com/apis/console) to get your client_id (use
     "Email Adress")
   * Check the [Google Analytics](http://www.google.com/analytics) admin panel (Sign in -> Admin -> Profile column ->
     Settings -> View ID) for the profile_id (don't forget to prefix the view ID by ga:)
 * Download the private key and put it somewhere on your server (for instance, you can put it in `app/bin/`).

At the end, you should have:

 * `client_id`: an email address which should look like `XXXXXXXXXXXX@developer.gserviceaccount.com`.
 * `profile_id`: a view ID which should look like `ga:XXXXXXXX`.
 * `private_key`: a PKCS12 certificate file

## Query

First, in order to request the Google Analytics service, simply create a request and configure it according
to your needs:

``` php
use Widop\GoogleAnalytics\Query;

$profileId = 'ga:XXXXXXXX';
$query = new Query($profileId);

$query->setStartDate(new \DateTime('-2months'));
$query->setEndDate(new \DateTime());

// See https://developers.google.com/analytics/devguides/reporting/core/dimsmets
$query->setMetrics(array('ga:visits' ,'ga:bounces'));
$query->setDimensions(array('ga:browser', 'ga:city'));

// See https://developers.google.com/analytics/devguides/reporting/core/v3/reference#sort
$query->setSorts(array('ga:country', 'ga:browser'));

// See https://developers.google.com/analytics/devguides/reporting/core/v3/reference#filters
$query->setFilters(array('ga:browser=~^Firefox'));

// See https://developers.google.com/analytics/devguides/reporting/core/v3/reference#segment
$query->setSegment('gaid::10');

// Default values :)
$query->setStartIndex(1);
$query->setMaxResults(10000);
$query->setPrettyPrint(false);
$query->setCallback(null);
```

## Client

A client allows you to request an access token for a specific account with the OAuth protocol according to your
information & your certificate. As it needs to request a token through the http protocol, the library internally uses
the [Wid'op Http Adapter library](https://github.com/widop/http-adapter) which allows to issue http requests.

``` php
use Widop\GoogleAnalytics\Client;
use Widop\HttpAdapter\CurlHttpAdapter;

$clientId = 'XXXXXXXXXXXX@developer.gserviceaccount.com';
$privateKeyFile = __DIR__.'/certificate.p12';
$httpAdapter = new CurlHttpAdapter();

$client = new Client($clientId, $privateKeyFile, $httpAdapter);
$token = $client->getAccessToken();
```

## Service

Now we have a request & a token, we can request the Google Analytics service :)

``` php
use Widop\GoogleAnalytics\Service;

$service = new Service($client);
$response = $service->query($query);
```

## Response

The response is a `Widop\GoogleAnalytics\Response` object which wraps all available informations:

``` php
$profileInfo = $response->getProfileInfo();
$kind = $response->getKind();
$id = $response->getId();
$query = $response->getQuery();
$selfLink = $response->getSelfLink();
$previousLink = $response->getPreviousLink();
$nextLink = $response->getNextLink();
$startIndex = $response->getStartIndex();
$itemsPerPage = $response->getItemsPerPage();
$totalResults = $response->getTotalResults();
$containsSampledData = $response->containsSampledData();
$columnHeaders = $response->getColumnHeaders();
$totalForAllResults = $response->getTotalsForAllResults();
$hasRows = $response->hasRows();
$rows = $response->getRows();
```

## Working example

If you want to have a working example of this bundle with symfony2 and sonata admin you can have a look at
[PrestaGoogleAnalyticsDashboardBundle](https://github.com/prestaconcept/PrestaGoogleAnalyticsDashboardBundle)
