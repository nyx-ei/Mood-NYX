@core @core_filepicker
Feature: Create folders in the file manager
  In order to create a directory structure in a file area
  As a user
  I need to create folders and subfolders in a file area

  @javascript @_bug_phantomjs
  Scenario: Create folders and subfolders
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And I log in as "admin"
    And I add a folder activity to course "Course 1" section "0"
    And I set the following fields to these values:
      | Name | Folder resource |
      | Description | The description |
    And I create "Folder 1" folder in "Files" filemanager
    And I open "Folder 1" folder from "Files" filemanager
    And I create "SubFolder 1" folder in "Files" filemanager
    When I open "Files" folder from "Files" filemanager
    Then I should see "Folder 1"
    And I open "Folder 1" folder from "Files" filemanager
    And I should see "SubFolder 1"
    And I press "Save and return to course"
