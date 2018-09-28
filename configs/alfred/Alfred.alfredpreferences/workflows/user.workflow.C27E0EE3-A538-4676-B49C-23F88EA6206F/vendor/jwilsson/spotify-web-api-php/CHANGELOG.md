# Changelog
## 1.11.1 (2017-06-12)
* Fixed an issue where some URI IDs would be properly cast to an array. ([713e8e7](https://github.com/jwilsson/spotify-web-api-php/commit/713e8e794cf1a7964ba0055f783516ac6f446715))

## 1.11.0 (2017-06-09)
* All methods accepting Album, Artist, Playlist, Track, or User IDs can now also accept Spotify URIs. (
    [1a47fa1](https://github.com/jwilsson/spotify-web-api-php/commit/1a47fa143771d3148d6cda9b59a2d500ed540a1d),
    [e71daeb](https://github.com/jwilsson/spotify-web-api-php/commit/e71daebdc7204ed9d2c704e2f5bfe0798ae3da60),
    [63dde40](https://github.com/jwilsson/spotify-web-api-php/commit/63dde405829e7894f2c0ce659ac5cc09cfa48bb7),
    [4bf29b1](https://github.com/jwilsson/spotify-web-api-php/commit/4bf29b13f64819513cd573cd86ce19ccd321ac40),
)
* Corrected `SpotifyWebAPI::getMySavedTracks` example. ([0eedf1c](https://github.com/jwilsson/spotify-web-api-php/commit/0eedf1cfbd6211eb41b99aedd71dabc9901d47b2))
* Updated `PHP_CodeSniffer` to `3.x`. ([60adb2c](https://github.com/jwilsson/spotify-web-api-php/commit/60adb2cb05b7adeccc271faeb8d6cceb6f949288))
* Travis builds now uses Trusty as the distribution. ([011524b](https://github.com/jwilsson/spotify-web-api-php/commit/011524b46c44c98b67bdd5930f534d40cc19804c))

## 1.10.1 (2017-04-29)
* Updated CA bundle. ([ff8d87e](https://github.com/jwilsson/spotify-web-api-php/commit/ff8d87eabbffc3e3c1e4e5d9145faf2ef1ef4932))
* Corrected the name of some Markdown example files. ([d6425f6](https://github.com/jwilsson/spotify-web-api-php/commit/d6425f610bfd377a4156a421f1246b50e57690ae))
* Corrected `SpotifyWebAPI::play()` example. ([ce2c08c](https://github.com/jwilsson/spotify-web-api-php/commit/ce2c08c90ca8d0fa420d15790dceb40ebb9f1297))
* Corrected inline method docs. ([d725d16](https://github.com/jwilsson/spotify-web-api-php/commit/d725d16a8726b19cc51da42557b97d00f4f52395))
* Removed stray `SpotifyWebApi` object in examples. ([7ef922b](https://github.com/jwilsson/spotify-web-api-php/commit/7ef922bf2fca35b0601578c51f870d481f5762d5))

## 1.10.0 (2017-04-12)
* Added Spotify Connect endpoints:
    * `SpotifyWebAPI::changeMyDevice()` ([21dd887](https://github.com/jwilsson/spotify-web-api-php/commit/21dd887271ba7c905fd2df0ea0f600421ef74baf))
    * `SpotifyWebAPI::changeVolume()` ([e9cdd79](https://github.com/jwilsson/spotify-web-api-php/commit/e9cdd797384559f83734626076cddecb15a195db))
    * `SpotifyWebAPI::getMyCurrentPlaybackInfo()` ([61f4cbd](https://github.com/jwilsson/spotify-web-api-php/commit/61f4cbd282cf3d89c8b135ad9a4eef6a07b9d5ff))
    * `SpotifyWebAPI::getMyCurrentTrack()` ([0f30a6b](https://github.com/jwilsson/spotify-web-api-php/commit/0f30a6b725f8f538e5eae8c893904e2045554881))
    * `SpotifyWebAPI::getMyDevices()` ([8b33f9d](https://github.com/jwilsson/spotify-web-api-php/commit/8b33f9d64f29aabb7ffd910ce8e09b46e043e2e4))
    * `SpotifyWebAPI::next()` ([9950c51](https://github.com/jwilsson/spotify-web-api-php/commit/9950c51790ba2ff05d0fb4e7360d6d745fd9ae1b))
    * `SpotifyWebAPI::pause()` ([b724c4a](https://github.com/jwilsson/spotify-web-api-php/commit/b724c4aefa6db88b397d7d19e24dae902f0d287c))
    * `SpotifyWebAPI::play()` ([825a632](https://github.com/jwilsson/spotify-web-api-php/commit/825a632fda5a8dadfe5ce5e782d768f0e7044c08))
    * `SpotifyWebAPI::previous()` ([90a97e1](https://github.com/jwilsson/spotify-web-api-php/commit/90a97e1d1b294d53a3a629febea78ba9eafb373a))
    * `SpotifyWebAPI::repeat()` ([1feebfe](https://github.com/jwilsson/spotify-web-api-php/commit/1feebfe365a140475f12d22151ec9fe5c4a11fe9))
    * `SpotifyWebAPI::seek()` ([0641a07](https://github.com/jwilsson/spotify-web-api-php/commit/0641a07cd79451681882273b630ff6313c570dbe))
    * `SpotifyWebAPI::shuffle()` ([d43268c](https://github.com/jwilsson/spotify-web-api-php/commit/d43268c4178cf1131eeed6de521f7bdf29a4b560))
* Complete documentation revamp. ([82d9fab](https://github.com/jwilsson/spotify-web-api-php/commit/82d9fabfc3f620068a114f55eff3a0e0803ff1a3))
* Made sure empty objects are correctly serialized to JSON objects instead of JSON arrays. ([b17682e](https://github.com/jwilsson/spotify-web-api-php/commit/b17682e6a1cf25c25c87a2a900cf6858a9c038b7))
## 1.9.0 (2017-03-24)
* Added the `SpotifyWebAPI::getMyRecentTracks()` method. ([4df889f](https://github.com/jwilsson/spotify-web-api-php/commit/4df889f2aa44c171d492f2784b45fd1155429b57))

## 1.8.0 (2017-03-05)
* Added the `SpotifyWebAPI::getMyRecentTracks()` method. ([fd8ea0d](https://github.com/jwilsson/spotify-web-api-php/commit/fd8ea0d70d690bbb0072a917530eba2b8c02e2a1))

## 1.7.0 (2017-02-25)
* The following methods can now also accept Spotify URIs:
    * `SpotifyWebAPI::addMyAlbums()` ([eecaea4](https://github.com/jwilsson/spotify-web-api-php/commit/eecaea4f8fe5d6554104da1018ba39002889c873))
    * `SpotifyWebAPI::addMyTracks()` ([1b63d90](https://github.com/jwilsson/spotify-web-api-php/commit/1b63d907d9127bba7ad013ce1fdf0617d894cd95))
    * `SpotifyWebAPI::deleteMyAlbums()` ([eecaea4](https://github.com/jwilsson/spotify-web-api-php/commit/eecaea4f8fe5d6554104da1018ba39002889c873))
    * `SpotifyWebAPI::deleteMyTracks()` ([eecaea4](https://github.com/jwilsson/spotify-web-api-php/commit/eecaea4f8fe5d6554104da1018ba39002889c873))
    * `SpotifyWebAPI::myAlbumsContains()` ([beb48e2](https://github.com/jwilsson/spotify-web-api-php/commit/beb48e2e397391a90129570ee1556347af70a95f))
    * `SpotifyWebAPI::myTracksContains()` ([edd72d7](https://github.com/jwilsson/spotify-web-api-php/commit/edd72d77ea834fff15c716e5f085e0d058966e0d))
* PHPUnit 5 is now used whenever possible. ([9892fe4](https://github.com/jwilsson/spotify-web-api-php/commit/9892fe481dd3193d719a251d43ce429d40202df8))

## 1.6.1 (2017-01-28)
* Bump for bad `1.6.0`.

## 1.6.0 (2017-01-28)
* Deprecated the following methods and replaced them with ([6aac5c6](https://github.com/jwilsson/spotify-web-api-php/commit/6aac5c6880017e0fadf7a48c5ba740dad2d9e617)):
    * `Request::getReturnAssoc()` -> `Request::getReturnType()`
    * `Request::setReturnAssoc()` -> `Request::setReturnType(Request::RETURN_ASSOC)`
    * `SpotifyWebAPI::getReturnAssoc()` -> `SpotifyWebAPI::getReturnType()`
    * `SpotifyWebAPI::setReturnAssoc()` -> `SpotifyWebAPI::setReturnType(SpotifyWebAPI::RETURN_ASSOC)`
* Added the following constants for use with `setReturnType()` ([6aac5c6](https://github.com/jwilsson/spotify-web-api-php/commit/6aac5c6880017e0fadf7a48c5ba740dad2d9e617)):
    * `Request::RETURN_ASSOC`
    * `Request::RETURN_OBJECT`
    * `SpotifyWebAPI::RETURN_ASSOC`
    * `SpotifyWebAPI::RETURN_OBJECT`
* Added docs on how to change the return type. ([10b47b5](https://github.com/jwilsson/spotify-web-api-php/commit/10b47b5cb4662ba53d45590cf39f9482a6dcb51e))

## 1.5.0 (2016-12-11)
* Added a `Request::getLastResponse()` method. ([21b72b0](https://github.com/jwilsson/spotify-web-api-php/commit/21b72b040ec10550649ded9050a431f890081f08))
* Added a `SpotifyWebAPI::getRequest` method.
([bab8924](https://github.com/jwilsson/spotify-web-api-php/commit/bab8924b1636e7d19f45722add8a0b769818983d))
* The `$tracks` option for `SpotifyWebAPI::deleteUserPlaylistTracks()` now also supports objects. ([ce230e7](https://github.com/jwilsson/spotify-web-api-php/commit/ce230e7c9c850ebe2837924bf0808ae5bb7a26af))
* Response compression will now be automatically negotiated by the client and server. ([3f4a643](https://github.com/jwilsson/spotify-web-api-php/commit/3f4a6434acb6bbcafe20d85bf09a74e0af2c403f))
* Made sure `SpotifyWebAPI::getAlbums()` can handle objects for the `$options` argument properly. ([42cf5d0](https://github.com/jwilsson/spotify-web-api-php/commit/42cf5d0345be270431156d270239d7538f0d2c82))
* Replaced `for`-loops with `array_map()`. ([cfc32b7](https://github.com/jwilsson/spotify-web-api-php/commit/cfc32b75226678274d39f631c27d80bcfd4941ec))
* CI tests are run on PHP 7.1. ([74cb084](https://github.com/jwilsson/spotify-web-api-php/commit/74cb084a24195ca24461587aa8977dda92f63dd2))
* Added documentation on error handling. ([57ba164](https://github.com/jwilsson/spotify-web-api-php/commit/57ba164ee15b6289358eec4998dff7796e7162f0))
* Fixed a typo in the `SpotifyWebAPI::reorderUserPlaylistTracks()` docs. ([b25dec4](https://github.com/jwilsson/spotify-web-api-php/commit/b25dec43039abbb57144a0ab6a2c45f5ac722c02))
* Fixed a typo in the `SpotifyWebAPI::getLastResponse()` docs. ([bdd3ecc](https://github.com/jwilsson/spotify-web-api-php/commit/bdd3ecc393ff83bc2d4af983c363cdaddb1b544b))

## 1.4.2 (2016-10-27)
* Array indexes in `SpotifyWebAPI::idToUri()` are now always reset to prevent undefined offset errors. ([ae8bd96](https://github.com/jwilsson/spotify-web-api-php/commit/ae8bd9673795747fad40ff4caf6b12f17c045fc5))

## 1.4.1 (2016-10-25)
* All requests will now be compressed using gzip. ([5eeabde](https://github.com/jwilsson/spotify-web-api-php/commit/5eeabde90d1c21832384f42d96c1208ce6fda287))

## 1.4.0 (2016-10-06)
* Marked `SpotifyWebAPI` class properties as `protected` instead of `private` to allow extending. ([f52468a](https://github.com/jwilsson/spotify-web-api-php/commit/f52468a7f68895dfad264675bf4274b0c272cfb2))
* Marked `Session` class properties as `protected` instead of `private` to allow extending. ([13e6d53](https://github.com/jwilsson/spotify-web-api-php/commit/13e6d536416717f999346caa96510183a5b82020))
* Marked `Request` class properties as `protected` instead of `private` to allow extending. ([be2b3f6](https://github.com/jwilsson/spotify-web-api-php/commit/be2b3f618b3a4aab7e6b12fa329c87d936675bb8))
* Moved docs from the `gh-pages` branch into `master`. ([7f638a1](https://github.com/jwilsson/spotify-web-api-php/commit/7f638a107c214c8b30319a230f7e16d2ac2a64a3))

## 1.3.4 (2016-09-23)
* Fixed a typo in the `Request::parseBody()` method added in `1.3.3`. ([13d3b94](https://github.com/jwilsson/spotify-web-api-php/commit/13d3b9417f0dc6de959867281ae0e4c9392f9c8d))

## 1.3.3 (2016-09-06)
* Moved the `Request` body parsing to its own method. ([ef60829](https://github.com/jwilsson/spotify-web-api-php/commit/ef608297271f0734a3a18b7a5e6ba40c1f41aa7a))
* All arrays are now using the short array syntax. ([Full diff](https://github.com/jwilsson/spotify-web-api-php/compare/5aa7ad833cf3bb7f0632e4cbe31d1d7898e6ca55...edfb711ec51ec9e76665f3e1bd53259ab9ea5a0e))
* Travis tests are now running on PHP nightlies as well. ([0cb8420](https://github.com/jwilsson/spotify-web-api-php/commit/0cb84209f0a7168392ace79db9ca68770f3f8c6d))
* Updated the inline `Request` docs for consistency. ([cf09e09](https://github.com/jwilsson/spotify-web-api-php/commit/cf09e0914aea66f6be192e1bc5fd3639dafcc399))

## 1.3.2 (2016-05-30)
* Improved the handling of `seed_*` parameters in `SpotifyWebAPI::getRecommendations()`. ([e6603dc](https://github.com/jwilsson/spotify-web-api-php/commit/e6603dc700c1105d10a25b3496b4e95f7238213f))
* Specified better Composer PHP version ranges so we don't break when a new major PHP versions is released. ([8dd7749](https://github.com/jwilsson/spotify-web-api-php/commit/8dd7749c331e0f035bbd9c5b7a2231875a0d6266))
* Fixed some minor code style issues in the tests. ([de5f7a8](https://github.com/jwilsson/spotify-web-api-php/commit/de5f7a897ae6755640317f834a8a19cd309524f5))

## 1.3.1 (2016-04-03)
* Fixed an issue where empty error responses weren't correctly handled. ([5f87cc5](https://github.com/jwilsson/spotify-web-api-php/commit/5f87cc56e4d6ae0c722c514423af5ee2c9c42b26))
* Fixed an issue where auth call exceptions would sometimes use the wrong message value. ([1b7951c](https://github.com/jwilsson/spotify-web-api-php/commit/1b7951c3aeb56dc83b84b0aac95a8ea0598ea8ec))

## 1.3.0 (2016-03-29)
* The following methods have been added:
    * `SpotifyWebAPI::getGenreSeeds()` ([88b750d](https://github.com/jwilsson/spotify-web-api-php/commit/88b750d7ec0879e54c37020f93310dccbdeec421))
    * `SpotifyWebAPI::getRecommendations()` ([28b7897](https://github.com/jwilsson/spotify-web-api-php/commit/28b7897820dd360a682a60b15426290137b9719f))
    * `SpotifyWebAPI::getMyTop()` ([edcafff](https://github.com/jwilsson/spotify-web-api-php/commit/edcafff3e1e465be5cccde477e97a4c4da49c643))
    * `SpotifyWebAPI::getAudioFeatures()` ([0759b29](https://github.com/jwilsson/spotify-web-api-php/commit/0759b2942b515d56dff5ab674e5564cae018234d))
* Minor inline docs updates ([745f117](https://github.com/jwilsson/spotify-web-api-php/commit/745f117e9163ee04634e0d5e1fa065cc1102108e), [35e9f57](https://github.com/jwilsson/spotify-web-api-php/commit/35e9f5755ffb889f00bf2ce414c14c8e077aee23), [50f040c](https://github.com/jwilsson/spotify-web-api-php/commit/50f040ce73644896578a698e4a968f8b5494949f))

## 1.2.0 (2015-12-01)
* The following methods have been added:
    * `SpotifyWebAPI::getMyPlaylists()` ([ea8f0a2](https://github.com/jwilsson/spotify-web-api-php/commit/ea8f0a2c23fb6bc4e496b6fb6885b5517626860f))
* Updated CA bundle. ([e6161fd](https://github.com/jwilsson/spotify-web-api-php/commit/e6161fd81d9851799315eb175a95ca8c001f31d3))

## 1.1.0 (2015-11-24)
* The following methods have been added:
    * `SpotifyWebAPI::addMyAlbums()` ([0027122](https://github.com/jwilsson/spotify-web-api-php/commit/0027122fe543ec9c3df9db3543be86683c7cd0d1))
    * `SpotifyWebAPI::deleteMyAlbums()` ([1d52172](https://github.com/jwilsson/spotify-web-api-php/commit/1d5217219095e0dded3f3afe300f72b91443d510))
    * `SpotifyWebAPI::getMySavedAlbums()` ([1bea486](https://github.com/jwilsson/spotify-web-api-php/commit/1bea4865d8323fa49d5b9f4ba4edc4cb68299115))
    * `SpotifyWebAPI::myAlbumsContains()` ([6f4ecfc](https://github.com/jwilsson/spotify-web-api-php/commit/6f4ecfc5ae929768f235367cf6deb259c8e75561))

## 1.0.0 (2015-10-13)
* **This release contains breaking changes, read through this list before updating.**
* The following, deprecated, methods have been removed:
    * `Session::refreshToken()` ([4d46e8c](https://github.com/jwilsson/spotify-web-api-php/commit/4d46e8ce5cda30924fb7afaa9886434a9a6e5c3c))
    * `Session::requestToken()` ([4d46e8c](https://github.com/jwilsson/spotify-web-api-php/commit/4d46e8ce5cda30924fb7afaa9886434a9a6e5c3c))
    * `SpotifyWebAPI::deletePlaylistTracks()` ([4d46e8c](https://github.com/jwilsson/spotify-web-api-php/commit/4d46e8ce5cda30924fb7afaa9886434a9a6e5c3c))
    * `SpotifyWebAPI::reorderPlaylistTracks()` ([4d46e8c](https://github.com/jwilsson/spotify-web-api-php/commit/4d46e8ce5cda30924fb7afaa9886434a9a6e5c3c))
    * `SpotifyWebAPI::replacePlaylistTracks()` ([4d46e8c](https://github.com/jwilsson/spotify-web-api-php/commit/4d46e8ce5cda30924fb7afaa9886434a9a6e5c3c))
* Added docs for the `market` parameter to the following methods:
    * `SpotifyWebAPI::getAlbums()` ([b83a131](https://github.com/jwilsson/spotify-web-api-php/commit/b83a1312a18039ba097c631194a01cef074f5f38))
    * `SpotifyWebAPI::getAlbumTracks()` ([c0a24d5](https://github.com/jwilsson/spotify-web-api-php/commit/c0a24d57cd15176df725ae8ea4217204a89c7ff8))
    * `SpotifyWebAPI::getMySavedTracks()` ([06ef152](https://github.com/jwilsson/spotify-web-api-php/commit/06ef15289c9533ce0d1a40e58821ae55aa4078da))
    * `SpotifyWebAPI::getTrack()` ([b48c2ff](https://github.com/jwilsson/spotify-web-api-php/commit/b48c2ff0e82603fefa37451cd83b317d78c2f11b))
    * `SpotifyWebAPI::getTracks()` ([ad7430a](https://github.com/jwilsson/spotify-web-api-php/commit/ad7430a6d91aa58eaace67e761623dffc43b6cdb))
    * `SpotifyWebAPI::getUserPlaylist()` ([a32ee7c](https://github.com/jwilsson/spotify-web-api-php/commit/a32ee7c2de48546f6a1b964ee7b379735e252cf2))
    * `SpotifyWebAPI::getUserPlaylistTracks()` ([0c104e8](https://github.com/jwilsson/spotify-web-api-php/commit/0c104e87db7076cbb363cd35ac8a307655c1c1c2))
* `Session::setRefreshToken()` has been removed, a refresh token is now passed directly to `Session::refreshAccessToken()` instead. ([62e7383](https://github.com/jwilsson/spotify-web-api-php/commit/62e7383d6cf732ff6c0fc4393711e29f1b12c69f))
* `Session::getExpires()` has been removed and `Session::getTokenExpiration()` has been added instead, returning the exact token expiration time. ([62e7383](https://github.com/jwilsson/spotify-web-api-php/commit/62e7383d6cf732ff6c0fc4393711e29f1b12c69f))
* The minimum required PHP version has been increased to 5.5 and support for PHP 7 has been added. ([b68ae3b](https://github.com/jwilsson/spotify-web-api-php/commit/b68ae3b524f462f3d3f0435617dd0cb21555a693), [6a8ac8d](https://github.com/jwilsson/spotify-web-api-php/commit/6a8ac8d309c4e6fbc076cb85897681fdb00f7a20))
* HTTP response headers returned by `Request::send()` and `SpotifyWebAPI::getLastResponse()` are now parsed to an array. ([9075bd3](https://github.com/jwilsson/spotify-web-api-php/commit/9075bd3289f02cee9b23ad596e308ad33dae0076))
* In `SpotifyWebAPI::deleteUserPlaylistTracks()`, `position` has been renamed to `positions` (note the extra "s"). This change was made to better align with the official Spotify docs. ([09f2636](https://github.com/jwilsson/spotify-web-api-php/commit/09f26369dc4c5f22ba8aee81cd858b9eb3584209))
* The `positions` argument to `SpotifyWebAPI::deleteUserPlaylistTracks()` now also accept `int`s. ([09f2636](https://github.com/jwilsson/spotify-web-api-php/commit/09f26369dc4c5f22ba8aee81cd858b9eb3584209))
* `SpotifyWebAPI::getArtistTopTracks()` now accepts an array of options. ([79543ac](https://github.com/jwilsson/spotify-web-api-php/commit/79543ac51850b91b4bf90a92c3482575524d0505))
* `Session::getAuthorizeUrl()` no longer sends empty query strings. ([c3e83e8](https://github.com/jwilsson/spotify-web-api-php/commit/c3e83e857560a299480ba7a41940835a0543c758))
* Stopped `SpotifyWebAPI::deleteUserPlaylistTracks()` from sending internal, leftover data. ([09f2636](https://github.com/jwilsson/spotify-web-api-php/commit/09f26369dc4c5f22ba8aee81cd858b9eb3584209))
* Clarified docs for `SpotifyWebAPI::followPlaylist()` and `SpotifyWebAPI::reorderUserPlaylistTracks()`. ([09f2636](https://github.com/jwilsson/spotify-web-api-php/commit/09f26369dc4c5f22ba8aee81cd858b9eb3584209))
* Fixed an issue where `SpotifyWebAPI::reorderUserPlaylistTracks()` couldn't reorder the first track. ([748592e](https://github.com/jwilsson/spotify-web-api-php/commit/748592ee7cc5a59f992d0ed0d49c1937931643cd))
* Better tests and coverage. ([09f2636](https://github.com/jwilsson/spotify-web-api-php/commit/09f26369dc4c5f22ba8aee81cd858b9eb3584209))

## 0.10.0 (2015-09-05)
* The following methods have been added:
    * `SpotifyWebAPI::getUserFollowedArtists()` ([b7142fa](https://github.com/jwilsson/spotify-web-api-php/commit/b7142fa466c307b56f285ab2aef546ecb8f998e2))

## 0.9.0 (2015-07-06)
* **This release contains breaking changes, read through this list before updating.**
* As we're moving closer to 1.0 the work to make the API more consistent and stable is continuing. This time with an effort to make method names and signatures more consistent.
* Thus, the following methods have been renamed and the old names are deprecated:
    * `SpotifyWebAPI::deletePlaylistTracks()` -> `SpotifyWebAPI::deleteUserPlaylistTracks()` ([8768328](https://github.com/jwilsson/spotify-web-api-php/commit/8768328aeeca1a82ebf652ad0ee557329ded6783))
    * `SpotifyWebAPI::reorderPlaylistTracks` -> `SpotifyWebAPI::reorderUserPlaylistTracks()` ([2ce8fc5](https://github.com/jwilsson/spotify-web-api-php/commit/2ce8fc51cc2a42d6b9055bc6ced1a0f777400486))
    * `SpotifyWebAPI::replacePlaylistTracks()` -> `SpotifyWebAPI::replaceUserPlaylistTracks()` ([6362510](https://github.com/jwilsson/spotify-web-api-php/commit/6362510344f746a37a75612d3f41030a60d81f2d))
* The following method arguments now also accepts strings:
    * `fields` in `SpotifyWebAPI::getUserPlaylistTracks()`. ([7a3c200](https://github.com/jwilsson/spotify-web-api-php/commit/7a3c200fb07ebcf11b60c5d778bbc4792855a5b9))
    * `fields` in `SpotifyWebAPI::getUserPlaylist()`. ([80cd7d0](https://github.com/jwilsson/spotify-web-api-php/commit/80cd7d08a8983a0519510445f122846d4939893d))
    * `album_type` in `SpotifyWebAPI::getArtistAlbums()`. ([4af0a53](https://github.com/jwilsson/spotify-web-api-php/commit/4af0a539df9b18550f6a7df337a07038775a5bed))
    * `ids` in `SpotifyWebAPI::userFollowsPlaylist()`. ([9cc11bb](https://github.com/jwilsson/spotify-web-api-php/commit/9cc11bba082e4accea0364d97a1c8486a9634971))
* A new method, `SpotifyWebAPI::getLastResponse()` has been introduced which allows for retrieval of the latest full response from the Spotify API. ([9b54074](https://github.com/jwilsson/spotify-web-api-php/commit/9b54074eb7ff3e223c1015580fb2dd975351975b))
* Lots of internal changes to increase code consistency and ensure full PSR-2 compatibility. ([2b8fda3](https://github.com/jwilsson/spotify-web-api-php/commit/2b8fda341176dddb8c9d4ef8ec808071efc54f49))
* Better handling of errors from cURL. ([c7b5529](https://github.com/jwilsson/spotify-web-api-php/commit/c7b5529cdac854de81fe87c79da5b318af15ca6a))

## 0.8.2 (2015-05-02)
* CA Root Certificates are now included with the library, allowing cURL to always find it. ([4ebee9b](https://github.com/jwilsson/spotify-web-api-php/commit/4ebee9b1b2ce53e622ace071f319e882d7c94cef))

## 0.8.1 (2015-03-29)
* Fixed an issue where `SpotifyWebAPI::updateUserPlaylist()` would fail without `name` set. ([39232f5](https://github.com/jwilsson/spotify-web-api-php/commit/39232f52c7efe090695dbf26e7dff1e1841db035))

## 0.8.0 (2015-03-22)
* **This release contains breaking changes, read through this list before updating.**
* The following methods have been renamed:
    * `Session::refreshToken()` -> `Session::refreshAccessToken()` ([7b6f31a](https://github.com/jwilsson/spotify-web-api-php/commit/7b6f31af4db435f1d3a94bef5758bdf3e864c65a))
    * `Session::requestToken()` -> `Session::requestAccessToken()` ([98c4a2a](https://github.com/jwilsson/spotify-web-api-php/commit/98c4a2a5b58e939bcfeba6ed72d07776c717698a))
* The following methods have been added:
    * `SpotifyWebAPI::currentUserFollows()` ([6dbab19](https://github.com/jwilsson/spotify-web-api-php/commit/6dbab19c39713126fa5172e959e157506a067f6d))
    * `SpotifyWebAPI::followArtistsOrUsers()` ([6dbab19](https://github.com/jwilsson/spotify-web-api-php/commit/6dbab19c39713126fa5172e959e157506a067f6d))
    * `SpotifyWebAPI::followPlaylist()` ([12ff351](https://github.com/jwilsson/spotify-web-api-php/commit/12ff3511deb732dbda11d547164eec34c5f47243))
    * `SpotifyWebAPI::getCategoriesList()` ([f09b4b8](https://github.com/jwilsson/spotify-web-api-php/commit/f09b4b8e9edcfe43cfad082123d49c5e2bbae873))
    * `SpotifyWebAPI::getCategory()` ([f09b4b8](https://github.com/jwilsson/spotify-web-api-php/commit/f09b4b8e9edcfe43cfad082123d49c5e2bbae873))
    * `SpotifyWebAPI::getCategoryPlaylists()` ([f09b4b8](https://github.com/jwilsson/spotify-web-api-php/commit/f09b4b8e9edcfe43cfad082123d49c5e2bbae873))
    * `SpotifyWebAPI::reorderPlaylistTracks()` ([0744904](https://github.com/jwilsson/spotify-web-api-php/commit/07449042143a87a5f8b0d73086c803bc4073407d))
    * `SpotifyWebAPI::unfollowArtistsOrUsers()` ([6dbab19](https://github.com/jwilsson/spotify-web-api-php/commit/6dbab19c39713126fa5172e959e157506a067f6d))
    * `SpotifyWebAPI::unfollowPlaylist()` ([12ff351](https://github.com/jwilsson/spotify-web-api-php/commit/12ff3511deb732dbda11d547164eec34c5f47243))
    * `SpotifyWebAPI::userFollowsPlaylist()` ([4293919](https://github.com/jwilsson/spotify-web-api-php/commit/42939192801bf69f915093f5d997ceab7599f8f9))
* The `$redirectUri` argument in `Session::__construct()` is now optional. ([8591ea8](https://github.com/jwilsson/spotify-web-api-php/commit/8591ea8f60373be953dceb41949bfc70aa1663c3))

## 0.7.0 (2014-12-06)
* The following methods to control the return type of all API methods were added:
    * `Request::getReturnAssoc()` ([b95bf3f](https://github.com/jwilsson/spotify-web-api-php/commit/b95bf3f3e4f702486e1de36633b131531b4a0546))
    * `Request::setReturnAssoc()` ([b95bf3f](https://github.com/jwilsson/spotify-web-api-php/commit/b95bf3f3e4f702486e1de36633b131531b4a0546))
    * `SpotifyWebAPI::getReturnAssoc()` ([b95bf3f](https://github.com/jwilsson/spotify-web-api-php/commit/b95bf3f3e4f702486e1de36633b131531b4a0546))
    * `SpotifyWebAPI::setReturnAssoc()` ([b95bf3f](https://github.com/jwilsson/spotify-web-api-php/commit/b95bf3f3e4f702486e1de36633b131531b4a0546))
* Added `fields` option to `SpotifyWebAPI::getUserPlaylist()`. ([c35e44d](https://github.com/jwilsson/spotify-web-api-php/commit/c35e44db2151e246a8b847653a2210d284125f7b))
* All methods now automatically send authorization headers (if a access token is supplied), increasing rate limits. ([a5e95a9](https://github.com/jwilsson/spotify-web-api-php/commit/a5e95a9015c076bfb30ca14336b6ca7f3a764e41))
* Lots of inline documentation improvements.

## 0.6.0 (2014-10-26)
* **This release contains breaking changes, read through this list before updating.**
* All static methods on `Request` have been removed. `Request` now needs to be instantiated before using. ([59207ac](https://github.com/jwilsson/spotify-web-api-php/commit/59207ac5705e8b43c1687b2e371e8133ddcf02fe))
* All methods that accepted the `limit` option now uses the correct Spotify default value if nothing has been specified. ([a291018](https://github.com/jwilsson/spotify-web-api-php/commit/a29101830b019e6acee0d03e1f11813a4a4a7a1b))
* It's now possible to specify your own `Request` object in `SpotifyWebAPI` and `Session` constructors. ([59207ac](https://github.com/jwilsson/spotify-web-api-php/commit/59207ac5705e8b43c1687b2e371e8133ddcf02fe))
* `SpotifyWebAPI::getArtistAlbums()` now supports the `album_type` option. ([1bd7014](https://github.com/jwilsson/spotify-web-api-php/commit/1bd7014f4d27d836e90128bf1c72dedcd7814645))
* `Request::send()` will only modify URLs when needed. ([0241f3b](https://github.com/jwilsson/spotify-web-api-php/commit/0241f3bf5c06dfb7a8ea0cd17f89d3ea06bb0688))

## 0.5.0 (2014-10-25)
* The following methods have been added:
    * `Session::getExpires()` ([c9c6da6](https://github.com/jwilsson/spotify-web-api-php/commit/c9c6da69333e74d8c8ae755998be8076e5e2deee))
    * `Session::getRefreshToken()` ([0d21147](https://github.com/jwilsson/spotify-web-api-php/commit/0d21147376196ab794d534197bc20227d67b6d14))
    * `Session::setRefreshToken()` ([ff83455](https://github.com/jwilsson/spotify-web-api-php/commit/ff83455439200f806eadc20d28e51b9d34502d78))
    * `SpotifyWebAPI::getFeaturedPlaylists()` ([c99537a](https://github.com/jwilsson/spotify-web-api-php/commit/c99537a907b802cfa5ee70b976ffe2f6e8135e6b))
    * `SpotifyWebAPI::getNewReleases()` ([7a8533c](https://github.com/jwilsson/spotify-web-api-php/commit/7a8533c0b0f8012cc84e360c8d472fce20a2fc48))
* The following options has been added:
    * `offset` and `limit` to `SpotifyWebAPI::getUserPlaylists()` ([3346857](https://github.com/jwilsson/spotify-web-api-php/commit/3346857ae82e8895741621d283ea57749ec9da48))
    * `offset` and `limit` to `SpotifyWebAPI::getUserPlaylistTracks()` ([1660600](https://github.com/jwilsson/spotify-web-api-php/commit/1660600fb35481e86a2ea8bd4bb915c0942b452a))
    * `fields` to `SpotifyWebAPI::getUserPlaylistTracks()` ([9a61003](https://github.com/jwilsson/spotify-web-api-php/commit/9a61003e904ec4b906487c28c91f1c0306d6ae0a))
    * `market` to `SpotifyWebAPI::getArtistAlbums()` ([98194dd](https://github.com/jwilsson/spotify-web-api-php/commit/98194dddd0e2e7f88f9b98429845c3d251afcbed))
    * `market` to `SpotifyWebAPI::search()` ([8883e79](https://github.com/jwilsson/spotify-web-api-php/commit/8883e799f997d477aa1b1c7ea44451c9087fb90b))
* Better handling of HTTP response codes in `Request::send()`. ([351be62](https://github.com/jwilsson/spotify-web-api-php/commit/351be62d3246dbd3beee2015a767d95ae6330e0a))
* Fixed a bug where `SpotifyWebAPIException` messages weren't correctly set. ([c764894](https://github.com/jwilsson/spotify-web-api-php/commit/c764894c4ab1e2fe7e872bcb1dc9670fdcde9135))
* Fixed various issues related to user playlists. ([9929d45](https://github.com/jwilsson/spotify-web-api-php/commit/9929d45c4dba49b3f76aa6ca0fde61ed4857a223))

## 0.4.0 (2014-09-01)
* **This release contains lots of breaking changes, read through this list before updating.**
* All methods which previously required a Spotify URI now just needs an ID. ([f1f14bd](https://github.com/jwilsson/spotify-web-api-php/commit/f1f14bd2ed0a77e1a6fdbee7091319c33cbfc634))
* `deletePlaylistTrack()` has been renamed to `deletePlaylistTracks()`. ([e54d703](https://github.com/jwilsson/spotify-web-api-php/commit/e54d703bd94d62a64058898e7d6cddf096b5a86a))
* When something goes wrong, a `SpotifyWebAPIException` is thrown. ([d98bb8a](https://github.com/jwilsson/spotify-web-api-php/commit/d98bb8aca96a73eb3495c3d84f5884117599d648))
* The `SpotifyWebAPI` methods are no longer static, you'll need to instantiate the class now. ([67c4e8b](https://github.com/jwilsson/spotify-web-api-php/commit/67c4e8ba1ce9e7f3bdd2d7acd6785e40a0949a4e))

## 0.3.0 (2014-08-23)
* The following methods have been added:
    * `SpotifyWebAPI::getMySavedTracks()` ([30c865d](https://github.com/jwilsson/spotify-web-api-php/commit/30c865d40771417646391bdd843dc1c7f5494c15))
    * `SpotifyWebAPI::myTracksContains()` ([3f99367](https://github.com/jwilsson/spotify-web-api-php/commit/3f9936710f1f1bdd11ea1cb36c87f101f94e0781))
    * `SpotifyWebAPI::addMyTracks()` ([20d80ef](https://github.com/jwilsson/spotify-web-api-php/commit/20d80efe183e5c484642d821eb37a6a53443f660))
    * `SpotifyWebAPI::deleteMyTracks()` ([ee17c69](https://github.com/jwilsson/spotify-web-api-php/commit/ee17c69b8d56c9466cfaac22d2243487dd3eff8c))
    * `SpotifyWebAPI::updateUserPlaylist()` ([5d5874d](https://github.com/jwilsson/spotify-web-api-php/commit/5d5874dd565e8156e123aed94f607eace3f28fb4))
    * `SpotifyWebAPI::deletePlaylistTrack()` ([3b17104](https://github.com/jwilsson/spotify-web-api-php/commit/3b1710494ce04ddae69b6edbccddc1b3530ca0fb))
    * `SpotifyWebAPI::deletePlaylistTrack()` ([3b5e23a](https://github.com/jwilsson/spotify-web-api-php/commit/3b5e23a30460ed4235259b23ff20eb1d0a87a43b))
* Added support for the Client Credentials Authorization Flow. ([0892e59](https://github.com/jwilsson/spotify-web-api-php/commit/0892e59022a15c79f6222ec82f596ca24af8fca3))
* Added support for more HTTP methods in `Request::send()`. ([d4df8c1](https://github.com/jwilsson/spotify-web-api-php/commit/d4df8c10f4f9f94ad4e0f2241bcbf0be0a81dede))

## 0.2.0 (2014-07-26)
* The following methods have been added:
    * `SpotifyWebAPI::getArtistRelatedArtists()` ([5a3ea0e](https://github.com/jwilsson/spotify-web-api-php/commit/5a3ea0e203d9b0285b1a671533aa64f81172eb49))
* Added `offset` and `limit` options for `SpotifyWebAPI::getAlbumTracks()` and `SpotifyWebAPI::getArtistAlbums()`. ([21c98ec](https://github.com/jwilsson/spotify-web-api-php/commit/21c98ec57f1714192d40b3f0736b3974cf1432f5), [8b0c417](https://github.com/jwilsson/spotify-web-api-php/commit/8b0c4170be46dcb6db25f942f264fa6fc77ac7fe))
* Replaced PSR-0 autoloading with PSR-4 autoloading. ([40878a9](https://github.com/jwilsson/spotify-web-api-php/commit/40878a93fcf158971d4d3674eeed7c44e44d1b97))
* Changed method signature of `Session::getAuthorizeUrl()` and added `show_dialog` option. ([8fe7a6e](https://github.com/jwilsson/spotify-web-api-php/commit/8fe7a6e5ada1c2195fdedfd560cb98cf7a422355), [57c36af](https://github.com/jwilsson/spotify-web-api-php/commit/57c36af84644393c801c86ca6542f4ab71d1eaf7))
* Added missing returns for `SpotifyWebAPI::getUserPlaylist()` and `SpotifyWebAPI::getUserPlaylistTracks()`. ([b8c87d7](https://github.com/jwilsson/spotify-web-api-php/commit/b8c87d7dfc830f6b4549ae564c1e3d78a6b6359c))
* Fixed a bug where search terms were double encoded. ([9f1eec6](https://github.com/jwilsson/spotify-web-api-php/commit/9f1eec6f4eeceb43a29f5f2748b88b1a1390b058))

## 0.1.0 (2014-06-28)
* Initial release
