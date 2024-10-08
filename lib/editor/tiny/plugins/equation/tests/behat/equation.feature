@editor @editor_tiny @tiny_equation
Feature: Equation editor
  To teach maths to students, I need to write equations

  @javascript
  Scenario: Create an equation using TinyMCE
    Given I log in as "admin"
    When I open my profile in edit mode
    And I set the field "Description" to "<p>Equation test</p>"
    # Set field on the bottom of page, so equation editor dialogue is visible.
    And I expand all fieldsets
    And I set the field "Picture description" to "Test"
    And I expand all toolbars for the "Description" TinyMCE editor
    And I click on the "Equation editor" button for the "Description" TinyMCE editor
    And the "class" attribute of "Edit equation using" "field" should contain "text-ltr"
    And I set the field "Edit equation using" to " = 1 \div 0"
    And I click on "\infty" "button"
    And I click on "Save equation" "button"
    And I click on "Update profile" "button"
    And I follow "Profile" in the user menu
    Then "\infty" "text" should exist

  @javascript
  Scenario: Edit an equation using TinyMCE
    Given I log in as "admin"
    When I open my profile in edit mode
    And I set the field "Description" to "<p>\( \pi \)</p>"
    # Set field on the bottom of page, so equation editor dialogue is visible.
    And I expand all fieldsets
    And I set the field "Picture description" to "Test"
    And I expand all toolbars for the "Description" TinyMCE editor
    And I click on the "Equation editor" button for the "Description" TinyMCE editor
    And the "class" attribute of "Edit equation using" "field" should contain "text-ltr"
    Then the field "Edit equation using" matches value " \pi "
    And I click on "Save equation" "button"
    And the field "Description" matches value "<p>\( \pi \)</p>"
