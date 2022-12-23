##Version 24.1.1
- Fix Error while serializing result data to generate callback
- Fix inRiver Callback to handle exception in the additional message validation and send it back to inRiver
- Fix code Magento Coding Standards
- Fix Sql Error in the handling of configurable relations

##Version 24.1.0
- Added clear logs on the inRiver callback when the api key is empty or invalid

##Version 1.2.2
- Added new lighter api endpoint route to get all skus and product types
- Fix code Magento Coding Standards 

##Version 1.2.1

Updated magento version to 2.4.1

- Added support for bundle products
- Remove custom update function for product attribute option API call as Magento has now is own function

##Version 1.2.0

Updated magento version to 2.4

- Fix issues with async calls because of non backward compatible changes
- Change the OperationRepositoryPlugin to be a BulkManagementPlugin as we need to be hooked differently to save the callbackURL
- Changed AddCallback plugin since the database changes required us to adapt with new code

Various Bug fixes
- Fix bug with configurable products that were losing relations with simple after importing cross-sell, upsell, related
- Fix bug with grouped that were losing its children when importing relations
- Updated unit tests 

##Version 0.6.0
- Added relations import
- Release on 2.3.5
