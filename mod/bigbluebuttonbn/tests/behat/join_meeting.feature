@mod @mod_bigbluebuttonbn @javascript
Feature: Test the ability to run the full meeting lifecycle (start to end)
  I can start a meeting then end it

  Background:
    Given a BigBlueButton mock server is configured
    And I enable "bigbluebuttonbn" "mod" plugin
    And the following config values are set as admin:
      | bigbluebuttonbn_userlimit_editable | 1 |
    And the following course exists:
      | name      | Test course |
      | shortname | C1          |
    And the following "users" exist:
      | username | firstname | lastname | email                 |
      | traverst | Terry     | Travers  | t.travers@example.com |
      | uraverst | Uerry     | Uravers  | u.uravers@example.com |
      | vraverst | Verry     | Vravers  | v.vravers@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | traverst | C1     | student |
      | uraverst | C1     | student |
      | vraverst | C1     | student |
    And the following "activity" exists:
      | course     | C1                  |
      | activity   | bigbluebuttonbn     |
      | name       | Room recordings     |
      | idnumber   | Room recordings     |
      | moderators | role:editingteacher |
      | wait       | 0                   |
      | userlimit  | 2                   |

  Scenario: Users should be able to join a meeting then end the meeting for themselves and
  return to the meeting page to join again.
    When I am on the "Room recordings" Activity page logged in as traverst
    And "Join session" "link" should exist
    And I click on "Join session" "link"
    And I switch to "bigbluebutton_conference" window
    And I click on "End Meeting" "link"
    # Selenium driver does not like the click action to be done before we
    # automatically close the window so we need to make sure that the window
    # is closed before.
    And I close all opened windows
    And I switch to the main window
    And I reload the page
    Then I should see "Room recordings"
    And I should see "This room is ready. You can join the session now."

  Scenario: Users can join the meeting until the maximum number of users has been reached
    When I am on the "Room recordings" Activity page logged in as traverst
    Then "Join session" "link" should exist
    And I click on "Join session" "link"
    And I switch to the main window
    And I log out
    And I am on the "Room recordings" Activity page logged in as uraverst
    And "Join session" "link" should exist
    And I click on "Join session" "link"
    And I switch to the main window
    And I log out
    And I am on the "Room recordings" Activity page logged in as vraverst
    And "Join session" "link" should not exist
    And I should see "The limit of how many users allowed in a session has been reached"
    And I log out
    And I am on the "Room recordings" Activity page logged in as admin
    And "Join session" "link" should not exist
    And I should see "The limit of how many users allowed in a session has been reached"
