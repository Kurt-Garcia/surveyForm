# Survey Improvement Areas Database Restructure

## Overview

This document outlines the improvements made to the survey improvement areas database structure. The original design had some normalization issues that have been addressed with a proper header-detail relationship.

## Previous Structure

The previous database design had a single table `survey_improvement_areas` with the following structure:

- `id` - Primary key
- `header_id` - Foreign key to survey_response_headers
- `area_category` - String field for the category name
- `area_details` - Text field storing multiple details as a concatenated string
- `is_other` - Boolean flag for "other" category
- `other_comments` - Text field for comments when is_other is true
- `timestamps` - Created/updated timestamps

## Issues with Previous Design

1. The `area_category` was repeated for each row in the table
2. The `area_details` field stored multiple details as a concatenated string, making it difficult to query individual details
3. The design did not follow proper normalization principles

## New Structure

The new design uses two tables to implement a proper header-detail relationship:

### 1. survey_improvement_categories (Header)

- `id` - Primary key
- `header_id` - Foreign key to survey_response_headers
- `category_name` - String field for the category name
- `is_other` - Boolean flag for "other" category
- `other_comments` - Text field for comments when is_other is true
- `timestamps` - Created/updated timestamps

### 2. survey_improvement_details (Details)

- `id` - Primary key
- `category_id` - Foreign key to survey_improvement_categories
- `detail_text` - Text field containing a single improvement detail
- `timestamps` - Created/updated timestamps

## Benefits of New Design

1. **Eliminates data redundancy**: Category information is stored once per category
2. **Improved data integrity**: Each detail is stored in its own row
3. **Better query capability**: Details can be easily queried and analyzed
4. **Follows normalization principles**: Proper relational database design
5. **More flexible for reporting**: Enables more advanced reporting and analysis

## Migration Path

A migration path has been provided to:

1. Create the new tables
2. Migrate existing data from the old structure to the new structure
3. Maintain backward compatibility during transition

## Models

- `SurveyImprovementCategory`: Represents the header/category
- `SurveyImprovementDetail`: Represents individual details
- `SurveyImprovementArea`: Legacy model maintained for backward compatibility

## Helpers

- `SurveyImprovementService`: Helper service to create records in both old and new format for backward compatibility

## Future Steps

Once all code has been updated to use the new structure, the old `survey_improvement_areas` table can be safely removed by enabling the commented line in the final migration.
