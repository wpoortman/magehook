Changelog
====================
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

#### Types of changes
- **Added** for new features.
- **Changed** for changes in existing functionality.
- **Deprecated** for soon-to-be removed features.
- **Removed** for now removed features.
- **Fixed** for any bug fixes.
- **Security** in case of vulnerabilities.

## [1.0.1]

## Changed
- Package name change from wpoortman/magehook-hook -> wpoortman/magehook
- Minor README improvements

## [1.0.0]

### Added
- Possibility to add webhooks.xml files to extensions
- Option to add custom validation options to a webhook event via UI components
- Option to add validation classes per webhook event
- Option to set additional data for validation or to send as additional data with webhook
- Unit test baby steps
- Ability to fetch webhook events based on a custom name
- Consumer type abstraction to write your own constumer types
- Guzzle for all HTTP responsibilities
- Core Magento interface reflection to transform model data to valid JSON
- Magento Message Queue compatibility
- Documentation (please read the README.md for more information)
- Webhook Management menu option
- Numerous dispatch options like Deploy Mode selection
- System configuration including Consumer Type sub-section options (read the docs)