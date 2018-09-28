## Constants


## Methods

### __construct


     SpotifyWebAPI\Session::__construct(string $clientId, string $clientSecret, string $redirectUri, \SpotifyWebAPI\Request $request)

Constructor<br>
Set up client credentials.

#### Arguments
* `$clientId` **string** - The client ID.
* `$clientSecret` **string** - The client secret.
* `$redirectUri` **string** - Optional. The redirect URI.
* `$request` **\SpotifyWebAPI\Request** - Optional. The Request object to use.



---


### getAuthorizeUrl


    string SpotifyWebAPI\Session::getAuthorizeUrl(array|object $options)

Get the authorization URL.

#### Arguments
* `$options` **array\|object** - Optional. Options for the authorization URL.
    * array scope Optional. Scope(s) to request from the user.
    * boolean show_dialog Optional. Whether or not to force the user to always approve the app. Default is false.
    * string state Optional. A CSRF token.



#### Return values
* **string** The authorization URL.


---


### getAccessToken


    string SpotifyWebAPI\Session::getAccessToken()

Get the access token.


#### Return values
* **string** The access token.


---


### getClientId


    string SpotifyWebAPI\Session::getClientId()

Get the client ID.


#### Return values
* **string** The client ID.


---


### getClientSecret


    string SpotifyWebAPI\Session::getClientSecret()

Get the client secret.


#### Return values
* **string** The client secret.


---


### getTokenExpiration


    integer SpotifyWebAPI\Session::getTokenExpiration()

Get the access token expiration time.


#### Return values
* **integer** A Unix timestamp indicating the token expiration time.


---


### getRedirectUri


    string SpotifyWebAPI\Session::getRedirectUri()

Get the client's redirect URI.


#### Return values
* **string** The redirect URI.


---


### getRefreshToken


    string SpotifyWebAPI\Session::getRefreshToken()

Get the refresh token.


#### Return values
* **string** The refresh token.


---


### refreshAccessToken


    boolean SpotifyWebAPI\Session::refreshAccessToken(string $refreshToken)

Refresh an access token.

#### Arguments
* `$refreshToken` **string** - The refresh token to use.


#### Return values
* **boolean** Whether the access token was successfully refreshed.


---


### requestCredentialsToken


    boolean SpotifyWebAPI\Session::requestCredentialsToken(array $scope)

Request an access token using the Client Credentials Flow.

#### Arguments
* `$scope` **array** - Optional. Scope(s) to request from the user.


#### Return values
* **boolean** True when an access token was successfully granted, false otherwise.


---


### requestAccessToken


    boolean SpotifyWebAPI\Session::requestAccessToken(string $authorizationCode)

Request an access token given an authorization code.

#### Arguments
* `$authorizationCode` **string** - The authorization code from Spotify.


#### Return values
* **boolean** True when the access token was successfully granted, false otherwise.


---


### setClientId


    void SpotifyWebAPI\Session::setClientId(string $clientId)

Set the client ID.

#### Arguments
* `$clientId` **string** - The client ID.


#### Return values
* **void** 


---


### setClientSecret


    void SpotifyWebAPI\Session::setClientSecret(string $clientSecret)

Set the client secret.

#### Arguments
* `$clientSecret` **string** - The client secret.


#### Return values
* **void** 


---


### setRedirectUri


    void SpotifyWebAPI\Session::setRedirectUri(string $redirectUri)

Set the client's redirect URI.

#### Arguments
* `$redirectUri` **string** - The redirect URI.


#### Return values
* **void** 


---

