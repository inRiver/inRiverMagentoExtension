##Version 23.3.2
- Fixed some exception handling and added more information in the messages

##Version 23.3.1
- Added support for text swatch
- Added support for hexadecimal color in visual swatch

##Version 23.3.0
- As an additional security layer, the configured inRiver API Key is now stored encrypted in the database in addition to being obscured in the admin panel.
- WARNING: This introduces a breaking change in the configuration. You must re-enter the API Key in order to encrypt it, as the system now assumes an encrypted key.
- Add functionality to skip errors and continue import when there is validation error in the import.
- Added a new setting to specify the amount of validation error allowed before stopping all the import. Set to 999999 by default.

##Version 23.2.0
- Better error message when an attribute option value is invalid.
- Continue processing other option value if one is invalid

##Version 23.1.1
- Fix inRiver Callback to handle exception in the additional message validation and send it back to inRiver
- Fix code Magento Coding Standards

##Version 23.1.0
- Added clear logs on the inRiver callback when the api key is empty or invalid

##Version 1.1.2
- Added new lighter api endpoint route to get all skus and product types
- Fix code Magento Coding Standards 
 
##Version 1.1.1
- Added delta import for  bundle options
- updated module to version 2.3.6
- fixed compatibility issue between 2.3.5 and 2.3.6 by making changes to the OperationRepositoryPlugin
 
 
##Version 1.1.0
 
Various Bug fixes
- Fix bug with configurable products that were losing relations with simple after importing cross-sell, upsell, related
- Fix bug with grouped that were losing its children when importing relations
 
##Version 0.6.0
 - Added relations import
 - Release on 2.3.5
