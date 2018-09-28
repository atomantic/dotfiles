<?php
namespace SpotifyWebAPI;

class Session
{
    protected $accessToken = '';
    protected $clientId = '';
    protected $clientSecret = '';
    protected $expirationTime = 0;
    protected $redirectUri = '';
    protected $refreshToken = '';
    protected $request = null;

    /**
     * Constructor
     * Set up client credentials.
     *
     * @param string $clientId The client ID.
     * @param string $clientSecret The client secret.
     * @param string $redirectUri Optional. The redirect URI.
     * @param Request $request Optional. The Request object to use.
     */
    public function __construct($clientId, $clientSecret, $redirectUri = '', $request = null)
    {
        $this->setClientId($clientId);
        $this->setClientSecret($clientSecret);
        $this->setRedirectUri($redirectUri);

        $this->request = $request ?: new Request();
    }

    /**
     * Get the authorization URL.
     *
     * @param array|object $options Optional. Options for the authorization URL.
     * - array scope Optional. Scope(s) to request from the user.
     * - boolean show_dialog Optional. Whether or not to force the user to always approve the app. Default is false.
     * - string state Optional. A CSRF token.
     *
     * @return string The authorization URL.
     */
    public function getAuthorizeUrl($options = [])
    {
        $options = (array) $options;

        $parameters = [
            'client_id' => $this->getClientId(),
            'redirect_uri' => $this->getRedirectUri(),
            'response_type' => 'code',
            'scope' => isset($options['scope']) ? implode(' ', $options['scope']) : null,
            'show_dialog' => !empty($options['show_dialog']) ? 'true' : null,
            'state' => isset($options['state']) ? $options['state'] : null,
        ];

        return Request::ACCOUNT_URL . '/authorize/?' . http_build_query($parameters);
    }

    /**
     * Get the access token.
     *
     * @return string The access token.
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Get the client ID.
     *
     * @return string The client ID.
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Get the client secret.
     *
     * @return string The client secret.
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * Get the access token expiration time.
     *
     * @return int A Unix timestamp indicating the token expiration time.
     */
    public function getTokenExpiration()
    {
        return $this->expirationTime;
    }

    /**
     * Get the client's redirect URI.
     *
     * @return string The redirect URI.
     */
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    /**
     * Get the refresh token.
     *
     * @return string The refresh token.
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * Refresh an access token.
     *
     * @param string $refreshToken The refresh token to use.
     *
     * @return bool Whether the access token was successfully refreshed.
     */
    public function refreshAccessToken($refreshToken)
    {
        $payload = base64_encode($this->getClientId() . ':' . $this->getClientSecret());

        $parameters = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ];

        $headers = [
            'Authorization' => 'Basic ' . $payload,
        ];

        $response = $this->request->account('POST', '/api/token', $parameters, $headers);
        $response = $response['body'];

        if (isset($response->access_token)) {
            $this->accessToken = $response->access_token;
            $this->expirationTime = time() + $response->expires_in;

            return true;
        }

        return false;
    }

    /**
     * Request an access token using the Client Credentials Flow.
     *
     * @param array $scope Optional. Scope(s) to request from the user.
     *
     * @return bool True when an access token was successfully granted, false otherwise.
     */
    public function requestCredentialsToken($scope = [])
    {
        $payload = base64_encode($this->getClientId() . ':' . $this->getClientSecret());

        $parameters = [
            'grant_type' => 'client_credentials',
            'scope' => implode(' ', $scope),
        ];

        $headers = [
            'Authorization' => 'Basic ' . $payload,
        ];

        $response = $this->request->account('POST', '/api/token', $parameters, $headers);
        $response = $response['body'];

        if (isset($response->access_token)) {
            $this->accessToken = $response->access_token;
            $this->expirationTime = time() + $response->expires_in;

            return true;
        }

        return false;
    }

    /**
     * Request an access token given an authorization code.
     *
     * @param string $authorizationCode The authorization code from Spotify.
     *
     * @return bool True when the access token was successfully granted, false otherwise.
     */
    public function requestAccessToken($authorizationCode)
    {
        $parameters = [
            'client_id' => $this->getClientId(),
            'client_secret' => $this->getClientSecret(),
            'code' => $authorizationCode,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->getRedirectUri(),
        ];

        $response = $this->request->account('POST', '/api/token', $parameters, []);
        $response = $response['body'];

        if (isset($response->refresh_token) && isset($response->access_token)) {
            $this->refreshToken = $response->refresh_token;
            $this->accessToken = $response->access_token;
            $this->expirationTime = time() + $response->expires_in;

            return true;
        }

        return false;
    }

    /**
     * Set the client ID.
     *
     * @param string $clientId The client ID.
     *
     * @return void
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * Set the client secret.
     *
     * @param string $clientSecret The client secret.
     *
     * @return void
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * Set the client's redirect URI.
     *
     * @param string $redirectUri The redirect URI.
     *
     * @return void
     */
    public function setRedirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;
    }
}
