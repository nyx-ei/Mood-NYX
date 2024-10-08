@enrol @enrol_meta @javascript
Feature: Enrolments are synchronised with meta courses
  In order to simplify enrolments in parent courses
  As a teacher
  I need to be able to set up meta enrolments

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@asd.com |
      | student2 | Student | 2 | student2@asd.com |
      | student3 | Student | 3 | student3@asd.com |
      | student4 | Student | 4 | student4@asd.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1C1 |
      | Course 2 | C2C2 |
      | Course 3 | C3C3 |
      | Course 4 | C4C4 |
    And the following "groups" exist:
      | name | course | idnumber |
      | Groupcourse 1 | C3C3 | G1 |
      | Groupcourse 2 | C3C3 | G2 |
    And the following "course enrolments" exist:
      | user | course | role | status |
      | student1 | C1C1 | student | 0 |
      | student2 | C1C1 | student | 0 |
      | student3 | C1C1 | student | 0 |
      | student4 | C1C1 | student | 0 |
      | student1 | C2C2 | student | 0 |
      | student2 | C2C2 | student | 0 |
      | student1 | C4C4 | student | 0 |
      | student2 | C4C4 | student | 1 |
    And the following config values are set as admin:
      | enableasyncbackup | 0 |
    And I log in as "admin"
    And I navigate to "Plugins > Enrolments > Manage enrol plugins" in site administration
    And I click on "Enable" "link" in the "Course meta link" "table_row"
    And I am on course index

  Scenario: Add meta enrolment instance without groups
    When I add "Course meta link" enrolment method in "Course 3" with:
      | Link course  | C1C1 |
    And I am on the "Course 3" "enrolled users" page
    Then I should see "No groups" in the "Student 1" "table_row"
    And I should see "No groups" in the "Student 4" "table_row"

  Scenario: Add meta enrolment instance with groups
    When I add "Course meta link" enrolment method in "Course 3" with:
      | Link course  | C1C1      |
      | Add to group | Groupcourse 1 |
    And I add "Course meta link" enrolment method in "Course 3" with:
      | Link course  | C2C2      |
      | Add to group | Groupcourse 2 |
    And I am on the "Course 3" "enrolled users" page
    Then I should see "Groupcourse 1" in the "Student 1" "table_row"
    And I should see "Groupcourse 1" in the "Student 2" "table_row"
    And I should see "Groupcourse 1" in the "Student 3" "table_row"
    And I should see "Groupcourse 1" in the "Student 4" "table_row"
    And I should see "Groupcourse 2" in the "Student 1" "table_row"
    And I should see "Groupcourse 2" in the "Student 2" "table_row"
    And I should not see "Groupcourse 2" in the "Student 3" "table_row"
    And I should not see "Groupcourse 2" in the "Student 4" "table_row"

  Scenario: Add meta enrolment instance with auto-created groups
    When I add "Course meta link" enrolment method in "Course 3" with:
      | Link course  | C1C1      |
      | Add to group | Create new group |
    And I am on the "Course 3" "enrolled users" page
    Then I should see "Course 1 course" in the "Student 1" "table_row"
    And I should see "Course 1 course" in the "Student 2" "table_row"
    And I should see "Course 1 course" in the "Student 3" "table_row"
    And I should see "Course 1 course" in the "Student 4" "table_row"
    And I am on the "Course 3" "groups" page
    And the "Groups" select box should contain "Course 1 course (4)"

  Scenario: Backup and restore of meta enrolment instance
    When I add "Course meta link" enrolment method in "Course 3" with:
      | Link course  | C1C1      |
      | Add to group | Groupcourse 1 |
    And I add "Course meta link" enrolment method in "Course 3" with:
      | Link course  | C2C2      |
    When I backup "Course 3" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I click on "Restore" "link" in the "test_backup.mbz" "table_row"
    And I press "Continue"
    And I set the field "targetid" to "1"
    And I click on "Continue" "button" in the ".bcs-new-course" "css_element"
    And I press "Next"
    And I set the field "Course name" to "Course 5"
    And I press "Next"
    And I press "Perform restore"
    And I trigger cron
    And I am on the "Course 5 copy 1" "enrolment methods" page
    Then I should see "Course meta link (Course 1)"
    And I should see "Course meta link (Course 2)"
    And I am on the "Course 5 copy 1" "enrolled users" page
    And I should see "Groupcourse 1" in the "Student 1" "table_row"
    And I should see "Groupcourse 1" in the "Student 2" "table_row"
    And I should see "Groupcourse 1" in the "Student 3" "table_row"
    And I should see "Groupcourse 1" in the "Student 4" "table_row"
    And I click on "[data-enrolinstancename='Course meta link (Course 2)'] a[data-action=showdetails]" "css_element" in the "Student 1" "table_row"
    And I should see "Course meta link (Course 2)" in the "Enrolment method" "table_row"

  Scenario: Unenrol a user from the course participants page that was enrolled via course meta link.
    Given I add "Course meta link" enrolment method in "Course 3" with:
      | Link course  | C4C4 |
    And I navigate to course participants
    # Suspended users can be unenrolled.
    When I click on "//a[@data-action='unenrol']" "xpath_element" in the "student2" "table_row"
    And I click on "Unenrol" "button" in the "Unenrol" "dialogue"
    Then I should not see "Student 2" in the "participants" "table"
