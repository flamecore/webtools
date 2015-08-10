# Changelog

All notable changes to the `Webtools` project will be documented in this file.


## NEXT - YYYY-MM-DD

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing


## 2.0.1 - 2015-08-10

### Fixed
- [WebpageAnalyzer] Accept URLs beginning with '//' in getAbsoluteUrl()


## 2.0.0 - 2015-07-22

### Added
- Added class OEmbed
- [WebpageAnalyzer] Added method isImageAccepted()
- [HtmlExplorer] Added method getSimpleXML()

### Fixed
- Bugfixes

### Removed
- Moved UserAgent component to external project


## 1.3.1 - 2015-07-05

### Fixed
- Bugfixes and improvements


## 1.3.0 - 2015-06-11

### Added
- [UserAgent] Added method UserAgent::isRealBrowser() etc.
- [UserAgent] Added FlameCore detection ('flamecore-*')
- [HttpClient] Added support for PUT and other requests
- [HttpClient] Allow setting User Agent string in constructor
- [WebpageAnalyzer] Allow using alternative HttpClient

### Fixed
- [UserAgent] Improved OS- and browser engine detection
- [UserAgent] Use precedence to check for UA
- [HttpClient] Use platform independent default cookie jar filename
- [HttpClient] Moved basic curl_setopt() calls to constructor
- [HttpClient] Fixed default headers
- Bugfixes and improvements


## 1.2.7 - 2015-07-05

### Fixed
- Bugfixes and improvements


## 1.2.6 - 2015-06-03

### Added
- [UserAgent] Added FlameCore detection ('flamecore-*')

### Fixed
- [HttpClient] Bugfixes


## 1.2.5 - 2015-06-01

### Fixed
- [HttpClient] Bugfixes and improvements


## 1.2.4 - 2015-05-29

### Fixed
- [HttpClient] Bugfixes


## 1.2.3 - 2015-05-21

### Added
- [UserAgent] Added Opera 15.0+ detection ('opera')
- [UserAgent] Added Maxthon detection ('maxthon')
- [UserAgent] Added Yandex.Browser detection ('yabrowser')


## 1.2.2 - 2015-05-15

### Added
- [UserAgent] Added KHTML engine detection ('khtml')
- [UserAgent] Added Lynx detection ('lynx')


## 1.2.1 - 2015-05-05

### Added
- [UserAgent] Added "Microsoft Edge" / "Spartan" detection ('edge')
- [UserAgent] Added "Baiduspider" detection ('baidubot')
- [UserAgent] Added 'Windows 8.1' and 'Windows 10' detection

### Fixed
- Minor fixes and improvements


## 1.2.0 - 2015-04-22

### Added
- Added class UserAgent
- Added class UserAgentStringParser

### Fixed
- Fixed bugs


## 1.1.2 - 2015-07-05

### Fixed
- Bugfixes and improvements


## 1.1.1 - 2015-06-11

### Fixed
- Bugfixes and improvements


## 1.1.0 - 2014-12-18

### Added
- [HtmlExplorer] Added static methods for quick access
- [HtmlExplorer] Throw exception on errors
- [HttpClient] Include headers in response information

### Removed
- Removed compatibility with PHP 5.3; Requires PHP >= 5.4 now


## 1.0.2 - 2015-07-05

### Fixed
- Bugfixes and improvements


## 1.0.1 - 2015-06-11

### Fixed
- Bugfixes and improvements


## 1.0.0 - 2014-12-14

First stable release
