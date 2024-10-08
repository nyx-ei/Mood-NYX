@core @core_course
Feature: Site home activities section
  In order to show a display activities in the frontpage
  As an admin
  I need to edit the frontpage section

  Background:
    Given the following config values are set as admin:
      | numsections | 1 |

  Scenario: Activities should appear in frontpage
    Given the following "activities" exist:
      | activity | course               | section | name                 | intro                  | idnumber |
      | assign   | Acceptance test site | 1       | Frontpage assignment | Assignment description | assign0  |
    When I log in as "admin"
    And I am on site homepage
    Then I should see "Frontpage assignment" in the "region-main" "region"

  @javascript
  Scenario: Section name does appears in frontpage
    Given the following "activities" exist:
      | activity | course               | section | name                 | intro                  | idnumber |
      | assign   | Acceptance test site | 1       | Frontpage assignment | Assignment description | assign0  |
    And I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I click on "Edit" "link" in the "region-main" "region"
    And I set the field "Section name" to "New section name"
    When I press "Save changes"
    And I should see "New section name" in the "region-main" "region"
    Then I turn editing mode off
    And I should see "New section name" in the "region-main" "region"

  @javascript
  Scenario: Section description appears in the frontpage
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I click on "Edit" "link" in the "region-main" "region"
    And I set the field "Description" to "New section description"
    When I press "Save changes"
    And I should see "New section description" in the "region-main" "region"
    Then I turn editing mode off
    And I should see "New section description" in the "region-main" "region"
