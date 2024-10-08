@mod @mod_feedback
Feature: Show users who have not responded to the feedback survey
  In order to harass students about completing the feedback
  As a teacher
  I need to see which students haven't responded

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | Teacher   | 1        |
      | student1 | Student   | 1        |
      | student2 | Student   | 2        |
      | student3 | Student   | 3        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
    And the following "groups" exist:
      | course | name | idnumber |
      | C1     | G1   | GI1      |
    And the following "group members" exist:
      | user     | group |
      | student1 | GI1   |
      | student2 | GI1   |
    And the following "groupings" exist:
      | name | course | idnumber |
      | GX1  | C1     | GXI1     |
    And the following "grouping groups" exist:
      | grouping | group  |
      | GXI1     | GI1    |

  Scenario: See users who have not responded
    # Set up a feedback.
    When I log in as "teacher1"
    And I add a feedback activity to course "Course 1" section "1" and I fill the form with:
      | Name                | Frogs                                             |
      | Description         | x                                                 |
      | Record user names   | User's name will be logged and shown with answers |
      | Access restrictions | Grouping: GX1                                     |
    And I am on the Frogs "feedback activity" page
    And I navigate to "Questions" in current page administration
    And I add a "Short text answer" question to the feedback with:
      | Question | Y/N? |
    And I log out

    # Go in as student 1 and do the feedback.
    And I am on the Frogs "feedback activity" page logged in as student1
    And I follow "Answer the questions"
    And I set the field "Y/N?" to "Y"
    And I press "Submit your answers"
    And I log out

    # Go in as teacher and check the users who haven't completed it.
    And I am on the Frogs "feedback activity" page logged in as teacher1
    And I navigate to "Responses" in current page administration
    And I select "Show non-respondents" from the "jump" singleselect
    # Should only show student 2; not student 1 (they did it) or 3 (not in grouping).
    Then I should see "Student 2"
    And I should not see "Student 1"
    And I should not see "Student 3"
