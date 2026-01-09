# Project Overview

This is OpenMage, a community-driven fork of the Magento eCommerce platform.

This project aims to provide a stable and secure version of Magento 1.x, with ongoing maintenance and improvements.

## Folder Structure

- `/app/code/community`: Contains the source code for the backend from community modules.
- `/app/code/core`: Contains the source code for the backend from core modules.
- `/app/code/local`: Contains the source code for the backend from local modules.
- `/app/design/adminhtml`: Contains the template and layout files for admin view.
- `/app/design/frontend`: Contains the template and layout files for frontend view.
- `/app/design/install`: Contains the template and layout files for installer.
- `/app/etc/modules`: Contains modules configuration.
- `/app/locale`: Contains localization files.
- `/cypress`: Contains cypress frontend test files.
- `/docs`: Contains documentation for the project, including API specifications and user guides.
- `/errors`: Contains source code for error pages.
- `/js`: Contains JavaScript files for the frontend.
- `/lib`: Contains third-party libraries and frameworks.
- `/media`: Contains media files, such as product images.
- `/shell`: Contains shell scripts for various tasks, such as database backups.
- `/skin`: Contains CSS and image files for the frontend.
- `/var`: Contains cache, logs, and session files.

## Libraries and Frameworks

- Zend Framework for core functionalities. See shardj/zf1 for more details.
- jQuery for DOM manipulation and AJAX requests.
- TinyMCE for rich text editing.
- Chart.js for data visualization.
- Flow.js for file uploads.
- jscolor for color picker functionality.
- Font Awesome for icons.
- Plain JavaScript (ES6+) for custom scripts. Avoid using prototype libraries.

## Coding Standards

- Use Cypress for end-to-end testing.
- Use PER2 coding standards for PHP.
- Use PHP-CodeSniffer to enforce coding standards.
- Use PHP-CS-Fixer for automatic code formatting.
- Use PHPMD mess detector to find potential issues.
- Use PHPStan for static analysis.
- Use PHPUnit for unit testing.
- Use Rector for automated refactoring.
- Follow PSR-12 coding standards for PHP code.
- Declare strict types in new PHP files.
- Use type hints for function parameters and return types for new methods.
- Use short array syntax `[]` for arrays in new code.
- Do not use underscores in new method names. Use camelCase instead.
- Use named parameters in new method calls where applicable.
- Ignore support for PHP versions below PHP 8.1 in new code.
- Update docblocks to use proper types and descriptions for new methods and classes.
- Update comments to reflect changes in code.
- Update tests to cover new functionality and changes in code.
- Update copyright notices in new files.
- Do not add return types in docblocks if type hints are used.
- Use strict comparisons (`===` and `!==`) instead of loose comparisons (`==` and `!=`) in new code.
- Avoid using empty() function in new code. Use explicit checks instead.

## UI guidelines

- Do not use prototype libraries. Use modern JavaScript (ES6+) features and libraries.
- Use responsive design principles to ensure the application works well on both desktop and mobile devices.
- Use a consistent color scheme and typography throughout the application.
- Use clear and concise language in all user-facing text.
- Ensure that all interactive elements are easily identifiable and accessible.
- Provide feedback to users for their actions, such as loading indicators and success/error messages.
- Test the application on multiple browsers to ensure compatibility.
- Ensure that the application is accessible to users with disabilities, following WCAG guidelines.
- Optimize the application for performance, minimizing load times and resource usage.
