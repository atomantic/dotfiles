## Constants

* **ACCOUNT_URL**
* **API_URL**
* **RETURN_ASSOC**
* **RETURN_OBJECT**

## Methods

### account


    array SpotifyWebAPI\Request::account(string $method, string $uri, array $parameters, array $headers)

Make a request to the "account" endpoint.

#### Arguments
* `$method` **string** - The HTTP method to use.
* `$uri` **string** - The URI to request.
* `$parameters` **array** - Optional. Query string parameters or HTTP body, depending on $method.
* `$headers` **array** - Optional. HTTP headers.


#### Return values
* **array** Response data.
    * array\|object body The response body. Type is controlled by `Request::setReturnType()`.
    * string headers Response headers.
    * int status HTTP status code.
    * string url The requested URL.


---


### api


    array SpotifyWebAPI\Request::api(string $method, string $uri, array $parameters, array $headers)

Make a request to the "api" endpoint.

#### Arguments
* `$method` **string** - The HTTP method to use.
* `$uri` **string** - The URI to request.
* `$parameters` **array** - Optional. Query string parameters or HTTP body, depending on $method.
* `$headers` **array** - Optional. HTTP headers.


#### Return values
* **array** Response data.
    * array\|object body The response body. Type is controlled by `Request::setReturnType()`.
    * string headers Response headers.
    * int status HTTP status code.
    * string url The requested URL.


---


### getLastResponse


    array SpotifyWebAPI\Request::getLastResponse()

Get the latest full response from the Spotify API.


#### Return values
* **array** Response data.
    * array\|object body The response body. Type is controlled by `Request::setReturnType()`.
    * array headers Response headers.
    * int status HTTP status code.
    * string url The requested URL.


---


### getReturnAssoc

_Deprecated: Use `Request::getReturnType()` instead._

    boolean SpotifyWebAPI\Request::getReturnAssoc()

Use `Request::getReturnType()` instead.


#### Return values
* **boolean** Whether the body is returned as an associative array or an stdClass.


---


### getReturnType


    string SpotifyWebAPI\Request::getReturnType()

Get a value indicating the response body type.


#### Return values
* **string** A value indicating if the response body is an object or associative array.


---


### send


    array SpotifyWebAPI\Request::send(string $method, string $url, array $parameters, array $headers)

Make a request to Spotify.<br>
You'll probably want to use one of the convenience methods instead.

#### Arguments
* `$method` **string** - The HTTP method to use.
* `$url` **string** - The URL to request.
* `$parameters` **array** - Optional. Query string parameters or HTTP body, depending on $method.
* `$headers` **array** - Optional. HTTP headers.


#### Return values
* **array** Response data.
    * array\|object body The response body. Type is controlled by `Request::setReturnType()`.
    * array headers Response headers.
    * int status HTTP status code.
    * string url The requested URL.


---


### setReturnAssoc

_Deprecated: Use `Request::setReturnType()` instead._

    void SpotifyWebAPI\Request::setReturnAssoc(boolean $returnAssoc)

Use `Request::setReturnType()` instead.

#### Arguments
* `$returnAssoc` **boolean** - Whether to return an associative array or an stdClass.


#### Return values
* **void** 


---


### setReturnType


    void SpotifyWebAPI\Request::setReturnType(string $returnType)

Set the return type for the response body.

#### Arguments
* `$returnType` **string** - One of the Request::RETURN_* constants.


#### Return values
* **void** 


---

