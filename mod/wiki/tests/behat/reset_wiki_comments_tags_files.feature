@mod @mod_wiki @javascript @_file_upload
Feature: Teachers can reset wiki pages, tags and files
  In order to remove wiki pages, tags and files
  As a teacher
  I need to be able to reset the pages, tags and files on the course level

  Background: Create a wiki, add a page, tag and file, and reset them
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activity" exists:
      | activity      | wiki                  |
      | course        | C1                    |
      | name          | Test wiki name        |
      | wikimode      | collaborative         |
    And I am on the "Test wiki name" "wiki activity" page logged in as teacher1
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | Test wiki content |
      | Tags | Test tag 1, Test tag 2, |
    And I press "Save"
    And I should see "Test tag 1" in the ".wiki-tags" "css_element"
    And I should see "Test tag 2" in the ".wiki-tags" "css_element"
    And I select "Comments" from the "jump" singleselect
    And I follow "Add comment"
    And I set the following fields to these values:
      | Comment | Test comment |
    And I press "Save changes"
    And I should see "Test comment"
    And I select "Files" from the "jump" singleselect
    And I press "Edit wiki files"
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I press "Save changes"
    And I should see "empty.txt"
    And I am on the "Course 1" "reset" page

  Scenario: Reset page, tags and files
    Given I press "Deselect all"
    And  I set the following fields to these values:
      | All wiki pages      | 1 |
      | All wiki tags       | 1 |
      | reset_wiki_comments | 1 |
    And I press "Reset course"
    And I click on "Reset course" "button" in the "Reset course?" "dialogue"
    And I should see "All wiki pages"
    And I should see "All wiki tags"
    And I should see "All comments"
    And I press "Continue"
    And I am on the "Test wiki name" "wiki activity" page
    And I press "Create page"
    When I select "View" from the "jump" singleselect
    Then I should not see "Test tag 1"
    And I should not see "Test tag 2"
    And I select "Comments" from the "jump" singleselect
    And I should not see "Test comment"
    And I select "Files" from the "jump" singleselect
    And I should not see "empty.txt"

  Scenario: Reset only tags
    Given I press "Deselect all"
    And I set the following fields to these values:
      | All wiki tags | 1 |
    When I press "Reset course"
    And I click on "Reset course" "button" in the "Reset course?" "dialogue"
    And I should not see "All wiki pages"
    And I should see "All wiki tags"
    And I should not see "All comments"
    And I press "Continue"
    And I am on the "Test wiki name" "wiki activity" page
    Then I should not see "Test tag 1"
    And I should not see "Test tag 2"
    And I select "Comments" from the "jump" singleselect
    And I should see "Test comment"
    And I select "Files" from the "jump" singleselect
    And I should see "empty.txt"

  Scenario: Reset only comments
    Given I press "Deselect all"
    And I set the following fields to these values:
      | reset_wiki_comments | 1 |
    When I press "Reset course"
    And I click on "Reset course" "button" in the "Reset course?" "dialogue"
    And I should not see "All wiki pages"
    And I should not see "All wiki tags"
    And I should see "All comments"
    And I press "Continue"
    And I am on the "Test wiki name" "wiki activity" page
    Then I should see "Test tag 1"
    And I should see "Test tag 2"
    And I select "Comments" from the "jump" singleselect
    And I should not see "Test comment"
    And I select "Files" from the "jump" singleselect
    And I should see "empty.txt"
