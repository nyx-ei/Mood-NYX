@mod @mod_book @core_tag @javascript
Feature: Edited book chapters handle tags correctly
  In order to get book chapters properly labelled
  As a user
  I need to introduce the tags while editing

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "activity" exists:
      | activity    | book                |
      | course      | C1                  |
      | idnumber    | book1               |
      | name        | Test book           |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |

  Scenario: Book chapter edition of custom tags works as expected
    Given I am on the "Test book" "book activity" page logged in as teacher1
    And I set the following fields to these values:
      | Chapter title | Dummy first chapter |
      | Content | Dream is the start of a journey |
      | Tags | Example, Chapter, Cool |
    And I press "Save changes"
    Then I should see "Example" in the ".book-tags" "css_element"
    And I should see "Chapter" in the ".book-tags" "css_element"
    And I should see "Cool" in the ".book-tags" "css_element"
    And I turn editing mode on
    And I follow "Edit chapter \"1. Dummy first chapter\""
    Then I should see "Example" in the ".form-autocomplete-selection" "css_element"
    Then I should see "Chapter" in the ".form-autocomplete-selection" "css_element"
    Then I should see "Cool" in the ".form-autocomplete-selection" "css_element"

  @javascript
  Scenario: Book chapter edition of standard tags works as expected
    Given the following "tags" exist:
      | name | isstandard |
      | OT1  | 1          |
      | OT2  | 1          |
      | OT3  | 1          |
    And I am on the "Test book" "book activity" page logged in as teacher1
    And I open the autocomplete suggestions list
    And I should see "OT1" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "OT2" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "OT3" in the ".form-autocomplete-suggestions" "css_element"
    When I set the following fields to these values:
      | Chapter title | Dummy first chapter |
      | Content | Dream is the start of a journey |
      | Tags | OT1, OT3 |
    And I press "Save changes"
    Then I should see "OT1" in the ".book-tags" "css_element"
    And I should see "OT3" in the ".book-tags" "css_element"
    And I should not see "OT2" in the ".book-tags" "css_element"
    And I turn editing mode on
    And I follow "Edit chapter \"1. Dummy first chapter\""
    And I should see "OT1" in the ".form-autocomplete-selection" "css_element"
    And I should see "OT3" in the ".form-autocomplete-selection" "css_element"
    And I should not see "OT2" in the ".form-autocomplete-selection" "css_element"
